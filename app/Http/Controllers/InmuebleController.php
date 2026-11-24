<?php
namespace App\Http\Controllers;

use App\Models\BusquedaGuardada;
use App\Models\Inmueble;
use App\Models\Inmobiliaria;
use App\Support\Imagen;
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
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Inmueble::with('inmobiliaria')->withCount('contactos')->latest();

        if (!$user->hasRole('admin')) {
            $query->deInmobiliaria($user->inmobiliaria->id ?? 0);
        }

        // Filtros granulares (GET /inmuebles?q=&estado=&tipo_operacion=&tipo_propiedad=&destacado=&precio_min=&precio_max=)
        if ($q = trim((string) $request->input('q', ''))) {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                  ->orWhere('direccion', 'like', "%{$q}%")
                  ->orWhere('ciudad', 'like', "%{$q}%");
            });
        }
        switch ($request->input('estado')) {
            case 'publicado': $query->where('publicado', 1)->where('cerrada', 0); break;
            case 'borrador': $query->where('publicado', 0)->where('cerrada', 0); break;
            case 'cerrada':   $query->where('cerrada', 1); break;
        }
        if ($op = $request->input('tipo_operacion')) { $query->where('tipo_operacion', $op); }
        if ($tp = $request->input('tipo_propiedad')) { $query->where('tipo_propiedad', $tp); }
        if ($request->input('destacado') === '1') { $query->where('destacado', 1); }
        if ($request->input('destacado') === '0') { $query->where('destacado', 0); }
        if (is_numeric($request->input('precio_min'))) { $query->where('precio', '>=', (float) $request->input('precio_min')); }
        if (is_numeric($request->input('precio_max'))) { $query->where('precio', '<=', (float) $request->input('precio_max')); }

        $inmuebles = $query->paginate(15)->withQueryString();

        // Aviso informativo cuando el plan está cerca del límite de publicaciones
        $avisoLimite = null;
        if (! $user->hasRole('admin') && $user->inmobiliaria) {
            $inmob = $user->inmobiliaria;
            $lim = $inmob->limiteInmuebles();
            $usadas = $inmob->inmueblesPublicados()->count();
            if ($lim > 0 && $usadas >= (int) floor($lim * 0.8)) {
                $avisoLimite = [
                    'usadas' => $usadas,
                    'lim' => $lim,
                    'restan' => max(0, $lim - $usadas),
                ];
            }
        }

        return view('inmuebles.index', compact('inmuebles', 'avisoLimite'));
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
            // Protección legal: el anunciante declara ser propietario o estar autorizado
            'declaracion_propietario' => ['required', 'accepted'],
            // Proximidades / ventajas del entorno (JSON desde el repeater del form)
            'proximidades_json' => 'nullable|string|max:6000',
            // Uso / habilitación para comerciales (kiosco, tienda de ropa, gastronomía...)
            'uso_detalle' => 'nullable|string|max:120',
        ];
    }

    // Endpoint para el navegador: puntúa elementos de Overpass que él consiguió (CORS) sin llamar a Overpass del server
    

    

    

    // Calcula el puntaje de entorno (Overpass/OSM) si el inmueble tiene coordenadas.
    // Nunca rompe el guardado: ante cualquier falla queda sin puntaje.
    

    // Botón "Recalcular puntaje" en la edición
    

    // Fallback: el navegador consulta Overpass (CORS habilitado) y manda los elementos; el server puntúa y guarda
    

    // Mensajes de validación en español claro (el default de Laravel traduce mal los snake_case)
    public static function mensajesValidacion(): array
    {
        return [
            'declaracion_propietario.required' => 'Debés marcar la declaración jurada: confirmás que sos propietario/a de la propiedad o que tenés autorización formal para publicarla.',
            'declaracion_propietario.accepted' => 'Tenés que aceptar la declaración jurada de propietario/autorización para poder publicar.',
            'titulo.required' => 'El título de la publicación es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'precio.required' => 'El precio es obligatorio.',
            'superficie_cubierta.required' => 'La superficie cubierta es obligatoria.',
            'habitaciones.required' => 'Indicá la cantidad de dormitorios (0 si no aplica).',
            'banos.required' => 'Indicá la cantidad de baños (0 si no aplica).',
        ];
    }

    // Normaliza el JSON de proximidades enviado por el formulario: [{nombre, metros}], máx 12, saneado
    public static function normalizarProximidades(?string $json): array
    {
        if (!$json) { return []; }
        $raw = json_decode($json, true);
        if (!is_array($raw)) { return []; }
        $out = [];
        foreach (array_slice($raw, 0, 12) as $p) {
            if (!is_array($p)) { continue; }
            $nombre = trim((string) ($p['nombre'] ?? ''));
            if ($nombre === '') { continue; }
            $metros = (isset($p['metros']) && is_numeric($p['metros'])) ? max(0, min(100000, (int) $p['metros'])) : null;
            $out[] = ['nombre' => mb_substr($nombre, 0, 80), 'metros' => $metros];
        }
        return $out;
    }

    public function store(Request $request)
    {
        // Log de todas las variables del request
        Log::info('Request Data:', $request->all());

        $data = $request->validate(static::reglasValidacion(), static::mensajesValidacion());

        // Asigna 1 si se marca la opción, de lo contrario 0
        $data['piscina']  = $request->has('piscina') ? 1 : 0;
        $data['jardin']   = $request->has('jardin') ? 1 : 0;
        $data['garaje']   = $request->has('garaje') ? 1 : 0;
        $data['ascensor'] = $request->has('ascensor') ? 1 : 0;
        $data['publicado'] = $request->has('publicado') ? 1 : 0;
        $data['declaracion_propietario'] = $request->has('declaracion_propietario') ? 1 : 0;
        $data['moneda'] = $data['moneda'] ?? 'ARS';
        $data['cocheras'] = $data['cocheras'] ?? 0;
        $data['proximidades'] = static::normalizarProximidades($request->input('proximidades_json')) ?: null;
        unset($data['proximidades_json']);

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


        // Guardado directo de imágenes si fueron adjuntadas en el alta (se normalizan: máx 1600px, comprimidas)
        $infosImagenes = [];
        if ($request->hasFile('imagenes')) {
            $images = $request->file('imagenes');
            $inmobiliaria = $inmueble->inmobiliaria;
            $limiteFotos = (int) ($inmobiliaria?->plan->limite_fotos ?? 10);
            $fotos = [];
            foreach (array_slice($images, 0, $limiteFotos) as $imagen) {
                if ($imagen->isValid()) {
                    $guardada = Imagen::guardar($imagen, public_path('images/fotosinmuebles'), (string) $inmueble->id, 1600);
                    $fotos[] = $guardada['nombre'];
                    if ($guardada['info']) {
                        $infosImagenes[] = $guardada['info'];
                    }
                }
            }
            if (!empty($fotos)) {
                $inmueble->fotos = $fotos;
                $inmueble->save();
            }
        }

        // Alerta a búsquedas guardadas (email a interesados) cuando se publica
        if (!empty($data['publicado'])) {
            BusquedaGuardada::notificarNuevaPropiedad($inmueble);
        }

        $mensaje = 'Inmueble creado correctamente.';
        if (!empty($infosImagenes)) {
            $mensaje .= ' Imágenes optimizadas automáticamente: ' . implode('; ', array_slice($infosImagenes, 0, 3)) . (count($infosImagenes) > 3 ? '…' : '');
        }

        return redirect()->route('inmuebles.index')->with('success', $mensaje);
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

        $data = $request->validate(static::reglasValidacion(), static::mensajesValidacion());

        // Gestionar prestaciones: asignar 1 si se marca, 0 si no se envía
        $data['piscina']  = $request->has('piscina') ? 1 : 0;
        $data['jardin']   = $request->has('jardin') ? 1 : 0;
        $data['garaje']   = $request->has('garaje') ? 1 : 0;
        $data['ascensor'] = $request->has('ascensor') ? 1 : 0;
        $data['publicado'] = $request->has('publicado') ? 1 : 0;
        $data['declaracion_propietario'] = $request->has('declaracion_propietario') ? 1 : 0;
        $data['moneda'] = $data['moneda'] ?? 'ARS';
        $data['cocheras'] = $data['cocheras'] ?? 0;
        $data['proximidades'] = static::normalizarProximidades($request->input('proximidades_json')) ?: null;
        unset($data['proximidades_json']);

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

        $infosImagenes = [];
        foreach ($images as $imagen) {
            if (!$imagen->isValid()) {
                continue;
            }
            $guardada = Imagen::guardar($imagen, public_path('images/fotosinmuebles'), (string) $inmuebleId, 1600);
            $nombre = $guardada['nombre'];
            if ($guardada['info']) {
                $infosImagenes[] = $guardada['info'];
            }

            if (!in_array($nombre, $fotos)) {
                $fotos[] = $nombre;
            }
        }

        $inmueble->fotos = array_values($fotos);
        $inmueble->save();

        $mensaje = 'Imágenes cargadas con éxito.';
        if (!empty($infosImagenes)) {
            $mensaje .= ' Se optimizaron automáticamente (' . implode('; ', array_slice($infosImagenes, 0, 3)) . (count($infosImagenes) > 3 ? '…' : '') . ').';
        }

        return response()->json(['success' => true, 'message' => $mensaje]);
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

        $extra = ($monto !== null) ? ' Comisión registrada: $' . number_format((float) $monto, 2, ',', '.') . ' (pendiente).' : '';

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
            ->with(['inmobiliaria.usuario.roles'])
            ->latest()
            ->get();

        $stats = [
            'inmobiliarias' => Inmobiliaria::activas()
                ->whereDoesntHave('usuario.roles', fn ($q) => $q->where('name', 'particular'))
                ->count(),
            'propiedades' => Inmueble::publicados()
                ->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))
                ->count(),
            'operaciones' => Inmueble::where('cerrada', 1)->count(),
        ];

        $inmobiliariasTop = Inmobiliaria::activas()
            ->whereDoesntHave('usuario.roles', fn ($q) => $q->where('name', 'particular'))
            ->withCount(['inmueblesPublicados', 'operaciones'])
            ->with('usuario')
            ->orderByDesc('inmuebles_publicados_count')
            ->take(6)
            ->get();

        // Recién publicadas: últimas propiedades agregadas al portal (sección "Nuevas publicaciones" de la home)
        $recientes = Inmueble::publicados()
            ->whereHas('inmobiliaria', function ($q) {
                $q->where('estado', 'activa');
            })
            ->with('inmobiliaria')
            ->latest()
            ->take(8)
            ->get();

        return view('welcome', compact('destacados', 'stats', 'inmobiliariasTop', 'recientes'));
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
