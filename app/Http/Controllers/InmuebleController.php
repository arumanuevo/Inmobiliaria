<?php
namespace App\Http\Controllers;

use App\Models\BusquedaGuardada;
use App\Models\Inmueble;
use App\Models\Inmobiliaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InmuebleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|inmobiliaria|particular')->except(['destacados']); // Solo admin, inmobiliarias y particulares; 'destacados' es público
    }

    // Mostrar listado de inmuebles (cada inmobiliaria solo ve los suyos)
    public function index()
    {
        $user = auth()->user();
        $query = Inmueble::with('inmobiliaria')->withCount('contactos')->latest();

        if (!$user->hasRole('admin')) {
            $query->deInmobiliaria($user->inmobiliaria->id ?? 0);
        }

        $inmuebles = $query->get();

        return view('inmuebles.index', compact('inmuebles'));
    }

    // Formulario para crear un inmueble
    public function create()
    {
        $this->authorize('create', Inmueble::class);

        return view('inmuebles.create');
    }

    // Reglas de validación compartidas entre el alta/edición propia y la carga asistida del admin
    public static function reglasValidacion(): array
    {
        return [
            'tipo_operacion'      => 'required|string|max:50',
            'tipo_propiedad'      => 'nullable|string|max:30',
            'moneda'              => 'nullable|in:ARS,USD',
            'ambientes'           => 'nullable|integer|min:0',
            'cocheras'            => 'nullable|integer|min:0',
            'expensas'            => 'nullable|numeric|min:0',
            'orientacion'         => 'nullable|string|max:20',
            'estado_propiedad'    => 'nullable|string|max:30',
            'titulo'              => 'required|string|max:255',
            'descripcion'         => 'required',
            'precio'              => 'required|numeric',
            'superficie_cubierta' => 'required|numeric',
            'habitaciones'        => 'required|integer',
            'banos'               => 'required|integer',
            // Campos adicionales
            'anio_construccion'   => 'nullable|integer',
            'direccion'           => 'nullable|string|max:255',
            'ciudad'              => 'nullable|string|max:100',
            'estado'              => 'nullable|string|max:100',
            'codigo_postal'       => 'nullable|string|max:20',
            'coordenadas'         => 'nullable|string|max:255',
            'propdom'             => 'nullable|string|max:255',
            'propnombre'          => 'nullable|string|max:255',
            'propdni'             => 'nullable|string|max:20',
            'propemail'           => 'nullable|email|max:255',
            'proptel'             => 'nullable|string|max:20',
        ];
    }

    public function store(Request $request)
    {
        // Log de todas las variables del request
        Log::info('Request Data:', $request->all());

        $data = $request->validate(static::reglasValidacion());

        // Asigna 1 si se marca la opción, de lo contrario 0
        $data['piscina']  = $request->has('piscina') ? 1 : 0;
        $data['jardin']   = $request->has('jardin') ? 1 : 0;
        $data['garaje']   = $request->has('garaje') ? 1 : 0;
        $data['ascensor'] = $request->has('ascensor') ? 1 : 0;
        $data['publicado'] = $request->has('publicado') ? 1 : 0;
        $data['moneda'] = $data['moneda'] ?? 'ARS';
        $data['cocheras'] = $data['cocheras'] ?? 0;

        $user = auth()->user();
        $data['usuario_id'] = $user->id;

        if (!$user->hasRole('admin')) {
            $inmobiliaria = $user->inmobiliaria;
            if (!$inmobiliaria) {
                return redirect()->route('inmobiliaria.perfil')
                    ->with('error', 'Completá primero el perfil de tu inmobiliaria.');
            }
            if ($inmobiliaria->estado !== 'activa') {
                return back()->with('error', 'Tu inmobiliaria aún no está activa. Esperá la aprobación del administrador.');
            }
            if ($inmobiliaria->inmuebles()->count() >= $inmobiliaria->limiteInmuebles()) {
                return back()->with('error', 'Alcanzaste el límite de publicaciones de tu plan (' . $inmobiliaria->limiteInmuebles() . '). Contactá al administrador para ampliar tu plan.');
            }
            $data['inmobiliaria_id'] = $inmobiliaria->id;
        }

        $inmueble = Inmueble::create($data);

        // Alerta a búsquedas guardadas (email a interesados) cuando se publica
        if (!empty($data['publicado'])) {
            BusquedaGuardada::notificarNuevaPropiedad($inmueble);
        }

        return redirect()->route('inmuebles.index')->with('success', 'Inmueble creado correctamente.');
    }

    // Mostrar detalles de un inmueble
    public function show(Inmueble $inmueble)
    {
        $this->authorize('view', $inmueble);

        return view('inmuebles.show', compact('inmueble'));
    }

    // Formulario para editar un inmueble
    public function edit(Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);

        return view('inmuebles.edit', compact('inmueble'));
    }

    public function update(Request $request, Inmueble $inmueble)
    {
        $yaPublicado = (bool) $inmueble->publicado;

        $data = $request->validate(static::reglasValidacion());

        // Gestionar prestaciones: asignar 1 si se marca, 0 si no se envía
        $data['piscina']  = $request->has('piscina') ? 1 : 0;
        $data['jardin']   = $request->has('jardin') ? 1 : 0;
        $data['garaje']   = $request->has('garaje') ? 1 : 0;
        $data['ascensor'] = $request->has('ascensor') ? 1 : 0;
        $data['publicado'] = $request->has('publicado') ? 1 : 0;
        $data['moneda'] = $data['moneda'] ?? 'ARS';
        $data['cocheras'] = $data['cocheras'] ?? 0;

        $this->authorize('update', $inmueble);

        $inmueble->update($data);

        // Si pasó de borrador a publicado, avisar a las búsquedas guardadas
        if (!$yaPublicado && $inmueble->publicado) {
            BusquedaGuardada::notificarNuevaPropiedad($inmueble);
        }

        return redirect()->route('inmuebles.index')->with('success', 'Inmueble actualizado correctamente.');
    }

    // Eliminar inmueble
    public function destroy(Inmueble $inmueble)
    {
        $this->authorize('delete', $inmueble);

        $inmueble->delete();
        return redirect()->route('inmuebles.index')->with('success', 'Inmueble eliminado correctamente.');
    }

    // Formulario para agregar fotos a un inmueble
    public function addPhotos(Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);

        $limiteFotos = (int) ($inmueble->inmobiliaria?->plan->limite_fotos ?? 10);
        $usadas = count($inmueble->fotos ?? []);

        return view('inmuebles.addPhotos', compact('inmueble', 'limiteFotos', 'usadas'));
    }

    // Formulario para borrar fotos de un inmueble
    public function deletePhotos(Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);

        return view('inmuebles.deletePhotos', compact('inmueble'));
    }

    // Borrar una foto de un inmueble
    public function destroyPhoto(Request $request, Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);

        $request->validate([
            'foto' => 'required|string',
        ]);

        $foto = $request->input('foto');
        $fotos = $inmueble->fotos ?? [];
        $index = array_search($foto, $fotos);

        if ($index !== false) {
            unset($fotos[$index]);
            $inmueble->fotos = array_values($fotos); // Reindexar el array
            $inmueble->save();

            // Borrar el archivo físico
            $path = public_path('images/fotosinmuebles/' . $foto);
            if (file_exists($path)) {
                unlink($path);
            }

            return redirect()->route('inmuebles.deletePhotos', $inmueble)->with('success', 'Foto borrada correctamente.');
        }

        return redirect()->route('inmuebles.deletePhotos', $inmueble)->with('error', 'Foto no encontrada.');
    }

    public function guardarImagenes(Request $request)
    {
        $tamañoMaximo = 6048; // Tamaño máximo en kilobytes

        $request->validate([
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:png,jpg,jpeg,gif,webp|max:' . $tamañoMaximo,
            'inmueble_id' => 'required|integer',
        ]);

        $images = $request->file('imagenes');
        $inmuebleId = $request->input('inmueble_id');
        $inmueble = Inmueble::findOrFail($inmuebleId);
        $this->authorize('update', $inmueble);

        $fotos = $inmueble->fotos ?? [];
        $inmobiliaria = $inmueble->inmobiliaria;
        $limiteFotos = (int) ($inmobiliaria?->plan->limite_fotos ?? 10);

        if (count($fotos) + count($images) > $limiteFotos) {
            return response()->json([
                'success' => false,
                'message' => "Tu plan permite hasta {$limiteFotos} fotos por publicación (ya tenés " . count($fotos) . " cargadas).",
            ]);
        }

        foreach ($images as $imagen) {
            $nombre = $inmuebleId . '_' . Str::uuid() . '.' . strtolower($imagen->getClientOriginalExtension());
            $imagen->move(public_path('images/fotosinmuebles'), $nombre);

            if (!in_array($nombre, $fotos)) {
                $fotos[] = $nombre;
            }
        }

        $inmueble->fotos = array_values($fotos);
        $inmueble->save();

        return response()->json(['success' => true, 'message' => 'Imágenes cargadas con éxito.']);
    }

    // Marcar propiedad como vendida/alquilada (se oculta del portal, suma al contador de operaciones)
    // con comisión opcional por el cierre (monto fijo o % sobre el precio)
    public function cerrar(Request $request, Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);

        $data = $request->validate([
            'tipo_cierre' => 'required|in:venta,alquiler',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'comision_monto' => 'nullable|numeric|min:0',
        ]);

        $pct = $data['comision_porcentaje'] ?? null;
        $monto = $data['comision_monto'] ?? null;
        if ($pct !== null && $monto === null && (float) $inmueble->precio > 0) {
            $monto = round((float) $inmueble->precio * ((float) $pct / 100), 2);
        }

        $inmueble->cerrada = 1;
        $inmueble->publicado = 0;
        $inmueble->tipo_cierre = $data['tipo_cierre'];
        $inmueble->cerrada_at = now();

        if ($pct !== null || $monto !== null) {
            $inmueble->comision_porcentaje = $pct !== null ? (float) $pct : null;
            $inmueble->comision_monto = $monto !== null ? (float) $monto : null;
            $inmueble->comision_estado = 'pendiente';
            $inmueble->comision_cobrada_at = null;
        }

        $inmueble->save();

        $extra = ($monto !== null) ? ' Comisión registrada: $' . number_format((float) $monto, 2) . ' (pendiente).' : '';

        return back()->with('success', 'Propiedad marcada como ' . $data['tipo_cierre'] . 'da. Ya no es visible en el portal público.' . $extra);
    }

    // Home del portal SaaS: destacadas de todas las inmobiliarias activas + datos reales del portal
    public function destacados()
    {
        $destacados = Inmueble::publicados()
            ->where('destacado', 1)
            ->whereHas('inmobiliaria', function ($q) {
                $q->where('estado', 'activa');
            })
            ->with('inmobiliaria')
            ->latest()
            ->get();

        $stats = [
            'inmobiliarias' => Inmobiliaria::activas()->count(),
            'propiedades' => Inmueble::publicados()
                ->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))
                ->count(),
            'operaciones' => Inmueble::where('cerrada', 1)->count(),
        ];

        $inmobiliariasTop = Inmobiliaria::activas()
            ->withCount(['inmueblesPublicados', 'operaciones'])
            ->with('usuario')
            ->orderByDesc('inmuebles_publicados_count')
            ->take(6)
            ->get();

        return view('welcome', compact('destacados', 'stats', 'inmobiliariasTop'));
    }

    public function toggleDestacado(Request $request, $id)
    {
        $request->validate([
            'destacado' => 'required|boolean',
        ]);

        $inmueble = Inmueble::findOrFail($id);
        $this->authorize('update', $inmueble);

        $destacado = (bool) $request->input('destacado');

        if ($destacado && !$inmueble->destacado) {
            $inmobiliaria = $inmueble->inmobiliaria;
            $limite = $inmobiliaria?->limiteDestacados() ?? 1;
            $actuales = Inmueble::deInmobiliaria($inmueble->inmobiliaria_id)
                ->where('destacado', 1)
                ->count();

            if ($actuales >= $limite) {
                return response()->json([
                    'success' => false,
                    'message' => "Tu plan permite hasta {$limite} propiedades destacadas.",
                ]);
            }
        }

        $inmueble->destacado = $destacado;
        $inmueble->save();

        return response()->json(['success' => true]);
    }
}
