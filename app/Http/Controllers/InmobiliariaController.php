<?php
namespace App\Http\Controllers;

use App\Models\Inmobiliaria;
use Illuminate\Http\Request;

class InmobiliariaController extends Controller
{
    // Directorio público de inmobiliarias activas
    public function index()
    {
        $inmobiliarias = Inmobiliaria::activas()
            ->withCount('inmueblesPublicados')
            ->latest()
            ->paginate(12);

        return view('inmobiliarias.index', compact('inmobiliarias'));
    }

    // Perfil público de una inmobiliaria con sus propiedades publicadas
    public function show(string $slug)
    {
        $inmobiliaria = Inmobiliaria::activas()
            ->where('slug', $slug)
            ->with('plan')
            ->firstOrFail();

        $destacados = $inmobiliaria->inmueblesPublicados()
            ->where('destacado', 1)
            ->latest()
            ->get();

        $inmuebles = $inmobiliaria->inmueblesPublicados()
            ->latest()
            ->paginate(9);

        return view('inmobiliarias.show', compact('inmobiliaria', 'destacados', 'inmuebles'));
    }

    // Formulario del perfil editable por la propia inmobiliaria
    public function perfil()
    {
        $inmobiliaria = auth()->user()->inmobiliaria;

        abort_if(!$inmobiliaria, 403, 'Aún no tenés un perfil de inmobiliaria.');

        return view('inmobiliarias.perfil', compact('inmobiliaria'));
    }

    public function actualizarPerfil(Request $request)
    {
        $inmobiliaria = auth()->user()->inmobiliaria;

        abort_if(!$inmobiliaria, 403, 'Aún no tenés un perfil de inmobiliaria.');

        $data = $request->validate([
            'nombre_comercial' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'codigo_postal' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'required|email|max:150',
            'sitio_web' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'color_tema' => 'nullable|string|max:7',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $dir = public_path('images/logos');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $nombre = $inmobiliaria->id . '_' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move($dir, $nombre);
            $data['logo'] = 'images/logos/' . $nombre;
        }

        if ($inmobiliaria->nombre_comercial !== $data['nombre_comercial']) {
            $data['slug'] = Inmobiliaria::generarSlug($data['nombre_comercial']);
        }

        $inmobiliaria->update($data);

        return redirect()->route('inmobiliaria.perfil')->with('success', 'Perfil actualizado correctamente.');
    }
}
