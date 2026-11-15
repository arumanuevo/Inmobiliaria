<?php
namespace App\Http\Controllers;

use App\Models\Inmobiliaria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InmobiliariaController extends Controller
{
    // Directorio público de inmobiliarias activas (con búsqueda por nombre/zona/cobertura)
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $query = Inmobiliaria::activas()
            ->withCount('inmueblesPublicados');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('nombre_comercial', 'like', "%{$q}%")
                   ->orWhere('ciudad', 'like', "%{$q}%")
                   ->orWhere('provincia', 'like', "%{$q}%")
                   ->orWhere('zonas_cobertura', 'like', "%{$q}%");
            });
        }

        $inmobiliarias = $query->latest()->paginate(12)->withQueryString();

        return view('inmobiliarias.index', compact('inmobiliarias', 'q'));
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
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9-]+$/', 'unique:inmobiliarias,slug,' . $inmobiliaria->id],
            'descripcion' => 'nullable|string',
            'zonas_cobertura' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'imagen_portada' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'galeria' => 'nullable|array|max:10',
            'galeria.*' => 'image|mimes:png,jpg,jpeg,webp|max:4096',
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

        $dir = public_path('images/inmobiliarias');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($request->hasFile('logo')) {
            $nombre = $inmobiliaria->id . '_logo_' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move($dir, $nombre);
            $data['logo'] = 'images/inmobiliarias/' . $nombre;
        }

        if ($request->hasFile('imagen_portada')) {
            $nombre = $inmobiliaria->id . '_portada_' . time() . '.' . $request->file('imagen_portada')->getClientOriginalExtension();
            $request->file('imagen_portada')->move($dir, $nombre);
            $data['imagen_portada'] = 'images/inmobiliarias/' . $nombre;
        }

        if ($request->hasFile('galeria')) {
            $galeria = $inmobiliaria->galeria ?? [];
            foreach ($request->file('galeria') as $img) {
                $nombre = $inmobiliaria->id . '_gal_' . time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move($dir, $nombre);
                $galeria[] = 'images/inmobiliarias/' . $nombre;
            }
            $data['galeria'] = array_values($galeria);
        }

        $data['slug'] = Str::slug($data['slug']);

        $inmobiliaria->update($data);

        return redirect()->route('inmobiliaria.perfil')->with('success', 'Perfil actualizado correctamente.');
    }
}
