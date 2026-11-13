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
        $this->middleware('role:admin')->except(['destacados']); // Excluir la acción 'destacados' del middleware de rol
    }

    // Mostrar listado de inmuebles
    public function index()
    {
        // Cargar todos los inmuebles para que el filtrado lo haga DataTables en el frontend
        $inmuebles = Inmueble::all();

        return view('inmuebles.index', compact('inmuebles'));
    }

    // Formulario para crear un inmueble
    public function create()
    {
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

        $data['usuario_id'] = auth()->id();

        Inmueble::create($data);

        return redirect()->route('inmuebles.index')->with('success', 'Inmueble creado correctamente.');
    }

    // Mostrar detalles de un inmueble
    public function show(Inmueble $inmueble)
    {
        return view('inmuebles.show', compact('inmueble'));
    }

    // Formulario para editar un inmueble
    public function edit(Inmueble $inmueble)
    {
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

        $inmueble->update($data);

        return redirect()->route('inmuebles.index')->with('success', 'Inmueble actualizado correctamente.');
    }

    // Eliminar inmueble
    public function destroy(Inmueble $inmueble)
    {
        $inmueble->delete();
        return redirect()->route('inmuebles.index')->with('success', 'Inmueble eliminado correctamente.');
    }

    // Formulario para agregar fotos a un inmueble
    public function addPhotos(Inmueble $inmueble)
    {
        return view('inmuebles.addPhotos', compact('inmueble'));
    }

    // Formulario para borrar fotos de un inmueble
    public function deletePhotos(Inmueble $inmueble)
    {
        return view('inmuebles.deletePhotos', compact('inmueble'));
    }

    // Borrar una foto de un inmueble
    public function destroyPhoto(Request $request, Inmueble $inmueble)
    {
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

    // Método para obtener propiedades destacadas
    public function destacados()
    {
        $destacados = Inmueble::where('destacado', 1)->get();
        return view('welcome', compact('destacados'));
    }

    public function toggleDestacado(Request $request, $id)
    {
        $request->validate([
            'destacado' => 'required|boolean',
        ]);

        $inmueble = Inmueble::findOrFail($id);
        $inmueble->destacado = $request->input('destacado');
        $inmueble->save();

        // Verificar si se ha alcanzado el límite de 6 inmuebles destacados
        $destacadosCount = Inmueble::where('destacado', 1)->count();
        if ($destacadosCount > 6) {
            // Desmarcar el inmueble más antiguo destacado
            $oldestDestacado = Inmueble::where('destacado', 1)->orderBy('updated_at')->first();
            $oldestDestacado->destacado = 0;
            $oldestDestacado->save();
        }

        return response()->json(['success' => true]);
    }
}
