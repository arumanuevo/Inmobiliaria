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
            ->whereDoesntHave('usuario.roles', function ($q) {
                $q->where('name', 'particular');
            })
            ->withCount(['inmueblesPublicados', 'operaciones'])
            ->with('usuario');

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

        // Propiedades publicadas con coordenadas para el mapa del perfil ("espacio digital")
        $inmueblesMapa = $inmobiliaria->inmueblesPublicados()
            ->whereNotNull('coordenadas')
            ->where('coordenadas', '!=', '')
            ->where('coordenadas', 'like', '%,%')
            ->latest()
            ->get(['id', 'titulo', 'coordenadas', 'precio', 'moneda', 'fotos', 'ciudad', 'tipo_operacion', 'publicado']);

        return view('inmobiliarias.show', compact('inmobiliaria', 'destacados', 'inmuebles', 'inmueblesMapa'));
    }

    // Ficha pública individual de una propiedad (SEO) dentro del perfil de su inmobiliaria
    public function propiedad(string $slug, \App\Models\Inmueble $inmueble)
    {
        $inmobiliaria = Inmobiliaria::activas()->where('slug', $slug)->with(['plan', 'usuario'])->firstOrFail();

        abort_if((int) $inmueble->inmobiliaria_id !== (int) $inmobiliaria->id, 404);
        abort_if(!$inmueble->publicado, 404);

        $inmueble->increment('visitas');

        $relacionados = $inmobiliaria->inmueblesPublicados()
            ->where('id', '!=', $inmueble->id)
            ->latest()
            ->take(3)
            ->get();

        // Comparativa de precio: esta propiedad vs. promedio por m² de su ciudad
        $comparativa = null;
        if ($inmueble->superficie_cubierta > 0 && $inmueble->ciudad) {
            $zona = \App\Models\Inmueble::publicados()
                ->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))
                ->whereRaw('LOWER(ciudad) = ?', [mb_strtolower((string) $inmueble->ciudad)])
                ->where('superficie_cubierta', '>', 0)
                ->where('id', '!=', $inmueble->id)
                ->get();

            if ($zona->count() >= 3) {
                $promedioM2 = $zona->map(fn ($i) => (float) $i->precio / (float) $i->superficie_cubierta)->avg();
                $m2 = (float) $inmueble->precio / (float) $inmueble->superficie_cubierta;
                if ($promedioM2 > 0) {
                    $comparativa = [
                        'pct' => (int) round((($m2 - $promedioM2) / $promedioM2) * 100),
                        'promedio_m2' => $promedioM2,
                        'm2' => $m2,
                        'n' => $zona->count(),
                    ];
                }
            }
        }

        return view('inmuebles.public_show', compact('inmobiliaria', 'inmueble', 'relacionados', 'comparativa'));
    }

    // Landing B2B para captar inmobiliarias ("Soy inmobiliaria")
    public function sumate()
    {
        // El plan "Particular" es la opción gratuita individual: se muestra aparte, no en la grilla B2B
        $planes = \App\Models\Plan::where('activo', 1)->where('slug', '!=', 'particular')->orderBy('precio_mensual')->get();
        $stats = [
            'inmobiliarias' => Inmobiliaria::activas()->count(),
            'propiedades' => \App\Models\Inmueble::publicados()->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))->count(),
            'operaciones' => \App\Models\Inmueble::where('cerrada', 1)->count(),
        ];

        return view('sumate', compact('planes', 'stats'));
    }

    // Vista previa del perfil público (solo el dueño): incluye borradores, con banner de preview
    public function preview()
    {
        $inmobiliaria = auth()->user()->inmobiliaria;

        abort_if(!$inmobiliaria, 403, 'Aún no tenés un perfil de inmobiliaria.');

        $destacados = $inmobiliaria->inmuebles()->where('destacado', 1)->latest()->get();
        $inmuebles = $inmobiliaria->inmuebles()->latest()->paginate(9);
        $inmueblesMapa = $inmobiliaria->inmuebles()
            ->whereNotNull('coordenadas')
            ->where('coordenadas', '!=', '')
            ->where('coordenadas', 'like', '%,%')
            ->latest()
            ->get(['id', 'titulo', 'coordenadas', 'precio', 'moneda', 'fotos', 'ciudad', 'tipo_operacion', 'publicado']);
        $preview = true;

        return view('inmobiliarias.show', compact('inmobiliaria', 'destacados', 'inmuebles', 'inmueblesMapa', 'preview'));
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
            // Personalización optativa del perfil público
            'tema' => 'nullable|in:moderno,elegante,minimal,corporativo',
            'mensaje_destacado' => 'nullable|string|max:255',
            'mostrar_operaciones' => 'nullable|boolean',
            'mostrar_mapa' => 'nullable|boolean',
            'mostrar_redes' => 'nullable|boolean',
        ]);

        $data['tema'] = $data['tema'] ?? 'moderno';
        $data['mostrar_operaciones'] = $request->has('mostrar_operaciones') ? 1 : 0;
        $data['mostrar_mapa'] = $request->has('mostrar_mapa') ? 1 : 0;
        $data['mostrar_redes'] = $request->has('mostrar_redes') ? 1 : 0;

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
