<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\InmuebleController;
use App\Models\Boleto;
use App\Models\BusquedaGuardada;
use App\Models\Inmueble;
use App\Models\Inmobiliaria;
use App\Models\Plan;
use App\Models\SolicitudCarga;
use App\Support\Imagen;
use App\Mail\CargaAsistidaListaMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function inmobiliarias()
    {
        $inmobiliarias = Inmobiliaria::with(['usuario', 'plan'])->latest()->paginate(15);
        $planes = Plan::where('activo', 1)->orderBy('precio_mensual')->get();

        return view('admin.inmobiliarias', compact('inmobiliarias', 'planes'));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,activa,suspendida',
        ]);

        $inmobiliaria = Inmobiliaria::findOrFail($id);
        $inmobiliaria->estado = $request->input('estado');
        $inmobiliaria->save();

        return back()->with('success', 'Estado de la inmobiliaria actualizado.');
    }

    public function cambiarVerificada(Request $request, $id)
    {
        if (! Inmobiliaria::columnaExiste('verificada')) {
            return back()->with('error', 'Falta correr saas_verificacion.sql en la base de datos (phpMyAdmin) para poder verificar inmobiliarias.');
        }
        $inmobiliaria = Inmobiliaria::findOrFail($id);
        $inmobiliaria->verificada = $request->boolean('verificada') ? 1 : 0;
        $inmobiliaria->save();
        return back()->with('success', $inmobiliaria->verificada ? 'Inmobiliaria marcada como verificada.' : 'Se quitó la verificación.');
    }

    // Diagnóstico de email (solo superadmin): envía un mail de prueba al superadmin y muestra
    // la configuración efectiva de mail/cola (con la password enmascarada)
    public function mailTest()
    {
        abort_unless(auth()->user()->esSuperAdmin(), 403, 'Solo el superadmin puede hacer la prueba de mail.');

        $to = config('app.superadmin_email');
        $smtp = (array) config('mail.mailers.smtp', []);
        unset($smtp['password']);
        $smtp['password'] = config('mail.mailers.smtp.password') ? '***configurada***' : '(vacía)';

        $config = [
            'mail.default (MAIL_MAILER)' => config('mail.default'),
            'queue.default (QUEUE_CONNECTION)' => config('queue.default'),
            'smtp.transport' => $smtp['transport'] ?? null,
            'smtp.scheme' => $smtp['scheme'] ?? null,
            'smtp.host (MAIL_HOST)' => $smtp['host'] ?? null,
            'smtp.port (MAIL_PORT)' => $smtp['port'] ?? null,
            'smtp.username (MAIL_USERNAME)' => $smtp['username'] ?? null,
            'smtp.password' => $smtp['password'],
            'mail.from.address' => config('mail.from.address'),
            'mail.from.name' => config('mail.from.name'),
        ];

        $resultado = ['ok' => false, 'error' => null];
        try {
            Mail::raw('Prueba de envío desde ' . config('app.name') . '. Si ves este mail, el SMTP del servidor funciona correctamente.', function ($m) use ($to) {
                $m->to($to)->subject('[' . config('app.name') . '] Mail de prueba');
            });
            $resultado['ok'] = true;
        } catch (\Throwable $e) {
            $resultado['error'] = $e->getMessage();
            Log::error('Mail de prueba falló: ' . $e->getMessage());
        }

        return view('admin.mail_test', compact('resultado', 'config', 'to'));
    }

    public function cambiarPlan(Request $request, $id)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:planes,id',
            'plan_vence_at' => 'nullable|date',
        ]);

        $inmobiliaria = Inmobiliaria::findOrFail($id);
        $inmobiliaria->plan_id = $request->input('plan_id');
        $inmobiliaria->plan_vence_at = $request->input('plan_vence_at');
        $inmobiliaria->save();

        $resumen = $inmobiliaria->aplicarLimitesDelPlan();
        $extra = ($resumen['despublicados'] || $resumen['destacados_quitados'])
            ? " Se despublicaron {$resumen['despublicados']} propiedades y se quitaron {$resumen['destacados_quitados']} destacados por los límites del nuevo plan."
            : '';

        return back()->with('success', 'Plan de la inmobiliaria actualizado.' . $extra);
    }

    // ===== Paquetes de suscripción (solo superadmin) =====

    protected function soloSuperAdmin(): void
    {
        abort_unless(auth()->user()->esSuperAdmin(), 403, 'Solo el superadmin puede administrar los paquetes.');
    }

    public function planes()
    {
        $this->soloSuperAdmin();

        $planes = Plan::withCount('inmobiliarias')->orderBy('precio_mensual')->get();

        return view('admin.planes', compact('planes'));
    }

    public function crearPlan(Request $request)
    {
        $this->soloSuperAdmin();

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|max:100|regex:/^[a-z0-9-]+$/|unique:planes,slug',
            'precio_mensual' => 'required|numeric|min:0',
            'limite_inmuebles' => 'required|integer|min:0',
            'limite_destacados' => 'required|integer|min:0',
            'limite_fotos' => 'required|integer|min:0',
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);
        $data['activo'] = 1;

        Plan::create($data);

        return back()->with('success', 'Paquete creado correctamente.');
    }

    public function actualizarPlan(Request $request, $id)
    {
        $this->soloSuperAdmin();

        $plan = Plan::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'precio_mensual' => 'required|numeric|min:0',
            'limite_inmuebles' => 'required|integer|min:0',
            'limite_destacados' => 'required|integer|min:0',
            'limite_fotos' => 'required|integer|min:0',
        ]);

        $plan->update($data);

        return back()->with('success', 'Paquete actualizado.');
    }

    public function togglePlan(Request $request, $id)
    {
        $this->soloSuperAdmin();

        $plan = Plan::findOrFail($id);

        if ($request->input('activo') == '0' && $plan->inmobiliarias()->count() > 0) {
            return back()->with('error', 'No se puede desactivar un paquete que tiene inmobiliarias asignadas. Reasignalas primero.');
        }

        $plan->activo = (bool) $request->input('activo');
        $plan->save();

        return back()->with('success', 'Estado del paquete actualizado.');
    }

    // ===== Operaciones concretadas (vendidas/alquiladas) y comisiones por cierre =====

    public function operaciones()
    {
        $operaciones = Inmueble::with('inmobiliaria')
            ->where('cerrada', 1)
            ->latest('cerrada_at')
            ->paginate(25);

        $base = Inmueble::where('cerrada', 1)->whereNotNull('comision_monto');
        $totales = [
            'pendiente' => (clone $base)->where('comision_estado', 'pendiente')->sum('comision_monto'),
            'cobrada' => (clone $base)->where('comision_estado', 'cobrada')->sum('comision_monto'),
            'cantidad' => Inmueble::where('cerrada', 1)->count(),
        ];

        return view('admin.operaciones', compact('operaciones', 'totales'));
    }

    public function cobrarOperacion(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,cobrada',
        ]);

        $inmueble = Inmueble::findOrFail($id);
        abort_unless($inmueble->cerrada, 404);

        $inmueble->comision_estado = $request->input('estado');
        $inmueble->comision_cobrada_at = $request->input('estado') === 'cobrada' ? now() : null;
        $inmueble->save();

        return back()->with('success', 'Estado de la comisión actualizado.');
    }

    // ===== Carga asistida: el admin carga propiedades a nombre de una inmobiliaria =====

    // Cola de solicitudes de carga asistida hechas por los clientes
    public function solicitudes(Request $request)
    {
        $estado = $request->input('estado');
        $query = SolicitudCarga::with(['inmobiliaria.usuario', 'inmueble'])->latest();
        if (in_array($estado, ['pendiente', 'en_carga', 'cargada', 'cancelada'], true)) {
            $query->where('estado', $estado);
        }
        $solicitudes = $query->paginate(20)->withQueryString();
        $pendientes = SolicitudCarga::where('estado', 'pendiente')->count();

        return view('admin.solicitudes', compact('solicitudes', 'estado', 'pendientes'));
    }

    public function cambiarEstadoSolicitud(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_carga,cancelada',
        ]);

        $sol = SolicitudCarga::findOrFail($id);
        $sol->estado = $request->input('estado');
        $sol->save();

        return back()->with('success', 'Solicitud #' . $sol->id . ' actualizada a "' . $sol->estado . '".');
    }

    public function formCargaAsistida($id)
    {
        $inmobiliariaAsistida = Inmobiliaria::findOrFail($id);
        $solicitud = request('solicitud') ? SolicitudCarga::find((int) request('solicitud')) : null;

        return view('inmuebles.create', compact('inmobiliariaAsistida', 'solicitud'));
    }

    public function cargarInmueble(Request $request, $id)
    {
        $inmobiliaria = Inmobiliaria::findOrFail($id);

        $data = $request->validate(InmuebleController::reglasValidacion(), InmuebleController::mensajesValidacion());

        $data['piscina']  = $request->has('piscina') ? 1 : 0;
        $data['jardin']   = $request->has('jardin') ? 1 : 0;
        $data['garaje']   = $request->has('garaje') ? 1 : 0;
        $data['ascensor'] = $request->has('ascensor') ? 1 : 0;
        $data['publicado'] = $request->has('publicado') ? 1 : 0;
        $data['declaracion_propietario'] = $request->has('declaracion_propietario') ? 1 : 0;
        $data['moneda'] = $data['moneda'] ?? 'ARS';
        $data['cocheras'] = $data['cocheras'] ?? 0;
        $data['inmobiliaria_id'] = $inmobiliaria->id;
        $data['usuario_id'] = $inmobiliaria->usuario_id;
        $data['carga_asistida'] = 1;
        $data['proximidades'] = InmuebleController::normalizarProximidades($request->input('proximidades_json')) ?: null;
        unset($data['proximidades_json']);

        $inmueble = Inmueble::create($data);


        if ($request->hasFile('imagenes')) {
            $images = $request->file('imagenes');
            $limiteFotos = (int) ($inmobiliaria->plan->limite_fotos ?? 10);
            $fotos = [];
            foreach (array_slice($images, 0, $limiteFotos) as $imagen) {
                if ($imagen->isValid()) {
                    $guardada = Imagen::guardar($imagen, public_path('images/fotosinmuebles'), (string) $inmueble->id, 1600);
                    $fotos[] = $guardada['nombre'];
                }
            }
            if (!empty($fotos)) {
                $inmueble->fotos = $fotos;
                $inmueble->save();
            }
        }

        if (!empty($data['publicado'])) {
            BusquedaGuardada::notificarNuevaPropiedad($inmueble);
        }

        // Si venía de una solicitud del cliente, marcarla cargada y avisarle por email
        $solicitudId = (int) $request->input('solicitud_id');
        if ($solicitudId) {
            $sol = SolicitudCarga::find($solicitudId);
            if ($sol) {
                $sol->estado = 'cargada';
                $sol->inmueble_id = $inmueble->id;
                $sol->atendida_at = now();
                $sol->save();

                try {
                    $dest = $inmobiliaria->email ?: ($inmobiliaria->usuario->email ?? null);
                    if ($dest) {
                        Mail::to($dest)->send(new CargaAsistidaListaMail($inmueble, $inmobiliaria));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Mail de carga asistida no enviado: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.solicitudes')
            ->with('success', 'Propiedad cargada para ' . $inmobiliaria->nombre_comercial . ' (carga asistida)' . ($solicitudId ? '. Solicitud #' . $solicitudId . ' marcada como cargada y avisada por email.' : '.'));
    }

    // Panel de boletos de suscripción
    public function boletos()
    {
        $boletos = Boleto::with(['inmobiliaria', 'plan'])->latest()->paginate(20);
        $inmobiliarias = Inmobiliaria::orderBy('nombre_comercial')->get();
        $planes = Plan::where('activo', 1)->orderBy('precio_mensual')->get();

        return view('admin.boletos', compact('boletos', 'inmobiliarias', 'planes'));
    }

    public function crearBoleto(Request $request)
    {
        $data = $request->validate([
            'inmobiliaria_id' => 'required|integer|exists:inmobiliarias,id',
            'plan_id' => 'nullable|integer|exists:planes,id',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'vence_at' => 'nullable|date',
        ]);

        Boleto::create($data + ['estado' => 'pendiente']);

        return back()->with('success', 'Boleto creado correctamente.');
    }

    public function cambiarEstadoBoleto(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,vencido,cancelado',
        ]);

        $boleto = Boleto::findOrFail($id);
        $boleto->estado = $request->input('estado');

        if ($boleto->estado === 'pagado') {
            $boleto->pagado_at = now();

            if ($boleto->plan_id) {
                $inmobiliaria = $boleto->inmobiliaria;
                $inmobiliaria->plan_id = $boleto->plan_id;
                $inmobiliaria->plan_vence_at = now()->addMonth()->toDateString();
                if ($inmobiliaria->estado === 'pendiente') {
                    $inmobiliaria->estado = 'activa';
                }
                $inmobiliaria->save();
                $inmobiliaria->aplicarLimitesDelPlan();
            }
        }

        $boleto->save();

        return back()->with('success', 'Estado del boleto actualizado.');
    }
}
