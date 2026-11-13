<?php
namespace App\Http\Controllers;

use App\Models\Inmueble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InmuebleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|inmobiliaria')->except(['destacados']); // Solo admin e inmobiliarias; 'destacados' es público
    }

    // Mostrar listado de inmuebles (cada inmobiliaria solo ve los suyos)
    public function index()
    {
        $user = auth()->user();
        $query = Inmueble::with('inmobiliaria')->latest();

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

    public function store(Request $request)
    {
        // Log de todas las variables del request
        Log::info('Request Data:', $request->all());

        $data = $request->validate([
            'tipo_operacion'      => 'required|string|max:50',
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
        ]);

        // Asigna 1 si se marca la opción, de lo contrario 0
        $data['piscina']  = $request->has('piscina') ? 1 : 0;
        $data['jardin']   = $request->has('jardin') ? 1 : 0;
        $data['garaje']   = $request->has('garaje') ? 1 : 0;
        $data['ascensor'] = $request->has('ascensor') ? 1 : 0;
        $data['publicado'] = $request->has('publicado') ? 1 : 0;

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

        Inmueble::create($data);

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
        $data = $request->validate([
            'tipo_operacion'      => 'required|string|max:50',
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
        ]);

        // Gestionar prestaciones: asignar 1 si se marca, 0 si no se envía
        $data['piscina']  = $request->has('piscina') ? 1 : 0;
        $data['jardin']   = $request->has('jardin') ? 1 : 0;
        $data['garaje']   = $request->has('garaje') ? 1 : 0;
        $data['ascensor'] = $request->has('ascensor') ? 1 : 0;
        $data['publicado'] = $request->has('publicado') ? 1 : 0;

        $this->authorize('update', $inmueble);

        $inmueble->update($data);

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

        return view('inmuebles.addPhotos', compact('inmueble'));
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
            'imagenes.*' => 'mimes:png,jpg,jpeg,gif|max:' . $tamañoMaximo,
            'inmueble_id' => 'required|integer',
        ]);

        $images = $request->file('imagenes');
        $inmuebleId = $request->input('inmueble_id');
        $inmueble = Inmueble::findOrFail($inmuebleId);
        $this->authorize('update', $inmueble);
        $fotos = $inmueble->fotos ?? []; // Obtener las fotos existentes o un array vacío

        if ($images && is_array($images)) {
            foreach ($images as $imagen) {
                $nombreOriginal = $imagen->getClientOriginalName();
                $nombreConPrefijo = $inmuebleId . '_' . $nombreOriginal;

                // Verificar si la imagen ya existe
                $path = public_path('images/fotosinmuebles' . $nombreConPrefijo);
                if (file_exists($path)) {
                    // Si existe, eliminar la imagen existente
                    unlink($path);
                }

                // Mover la imagen al directorio public/images
                $imagen->move(public_path('images/fotosinmuebles'), $nombreConPrefijo);

                // Actualizar el array de fotos
                if (!in_array($nombreConPrefijo, $fotos)) {
                    $fotos[] = $nombreConPrefijo;
                }
            }

            $inmueble->fotos = $fotos; // Asignar el array de fotos actualizado
            $inmueble->save();
        } else {
            return response()->json(['success' => false, 'message' => 'No se han seleccionado imágenes.']);
        }

        return response()->json(['success' => true, 'message' => 'Imágenes cargadas con éxito.']);
    }

    // Método para obtener propiedades destacadas (solo publicadas, de inmobiliarias activas)
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
        return view('welcome', compact('destacados'));
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
