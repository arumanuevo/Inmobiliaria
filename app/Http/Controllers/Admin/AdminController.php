<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boleto;
use App\Models\Inmobiliaria;
use App\Models\Plan;

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
