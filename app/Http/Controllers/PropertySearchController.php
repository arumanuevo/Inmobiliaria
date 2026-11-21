<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inmobiliaria;
use App\Models\Inmueble;

class PropertySearchController extends Controller
{
    public function search(Request $request)
    {
        // Mismos filtros que usan las alertas de búsquedas guardadas (Inmueble::scopeAplicarFiltros)
        $query = Inmueble::publicados()
            ->whereHas('inmobiliaria', function ($q) {
                $q->where('estado', 'activa');
            })
            ->with(['inmobiliaria.usuario.roles'])
            ->aplicarFiltros($request->only([
                'inmobiliaria', 'tipo_anunciante', 'tipo_operacion', 'tipo_propiedad', 'moneda', 'uso_comercial',
                'ambientes_min', 'cocheras_min', 'precio_min', 'precio_max',
                'habitaciones', 'banos', 'superficie_min', 'superficie_max', 'ciudad',
                'geo_lat', 'geo_lng', 'radio_km',
            ]));

        $properties = $query->paginate(6);

        return view('inmuebles.partials.property_results', compact('properties'));
    }

    public function index()
    {
        $inmobiliarias = Inmobiliaria::activas()->with('usuario')->orderBy('nombre_comercial')->get();
        $tipos = Inmueble::tiposPropiedad();
        $ciudades = Inmueble::publicados()
            ->whereNotNull('ciudad')
            ->where('ciudad', '!=', '')
            ->distinct()
            ->orderBy('ciudad')
            ->pluck('ciudad');

        return view('inmuebles.search', compact('inmobiliarias', 'tipos', 'ciudades'));
    }

    // Página SEO por zona: /propiedades-en-{venta|alquiler}-en-{ciudad}
    public function porZona(string $operacion, string $ciudad)
    {
        abort_unless(in_array($operacion, ['venta', 'alquiler']), 404);

        $ciudadNombre = str_replace('-', ' ', $ciudad);

        $propiedades = Inmueble::publicados()
            ->whereHas('inmobiliaria', function ($q) {
                $q->where('estado', 'activa');
            })
            ->where('tipo_operacion', $operacion)
            ->whereRaw('LOWER(ciudad) = ?', [mb_strtolower($ciudadNombre)])
            ->with(['inmobiliaria.usuario.roles'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('inmuebles.zona', compact('propiedades', 'operacion', 'ciudadNombre'));
    }

    // Sitemap XML para SEO
    public function sitemap()
    {
        $urls = [
            route('inicio'),
            route('inmobiliarias.index'),
            route('propiedades.index'),
            route('propiedades.mapa'),
            route('contacto'),
        ];

        foreach (Inmobiliaria::activas()->get() as $inm) {
            $urls[] = route('inmobiliarias.show', $inm->slug);
        }

        foreach (Inmueble::publicados()->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))->with('inmobiliaria')->get() as $p) {
            if ($p->inmobiliaria) {
                $urls[] = route('propiedad.show', [$p->inmobiliaria->slug, $p->id]);
            }
        }

        $ciudades = Inmueble::publicados()->whereNotNull('ciudad')->where('ciudad', '!=', '')->distinct()->pluck('ciudad');
        foreach ($ciudades as $c) {
            $slug = \Illuminate\Support\Str::slug($c);
            $urls[] = route('propiedades.zona', ['operacion' => 'venta', 'ciudad' => $slug]);
            $urls[] = route('propiedades.zona', ['operacion' => 'alquiler', 'ciudad' => $slug]);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (array_unique($urls) as $u) {
            $xml .= '  <url><loc>' . htmlspecialchars($u, ENT_XML1) . '</loc></url>' . "\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    // Vista de mapa con marcadores (búsqueda por área visible)
    public function mapa()
    {
        $tipos = Inmueble::tiposPropiedad();

        return view('inmuebles.mapa', compact('tipos'));
    }

    // JSON de propiedades publicadas con coordenadas, filtradas por filtros y opcionalmente por bounds
    public function apiMapa(Request $request)
    {
        $query = Inmueble::publicados()
            ->whereHas('inmobiliaria', function ($q) {
                $q->where('estado', 'activa');
            })
            ->whereNotNull('coordenadas')
            ->where('coordenadas', '!=', '')
            ->where('coordenadas', 'like', '%,%')
            ->with(['inmobiliaria.usuario.roles'])
            ->aplicarFiltros($request->only([
                'inmobiliaria', 'tipo_anunciante', 'tipo_operacion', 'tipo_propiedad', 'moneda', 'uso_comercial',
                'ambientes_min', 'cocheras_min', 'precio_min', 'precio_max',
                'habitaciones', 'banos', 'superficie_min', 'superficie_max', 'ciudad',
                'geo_lat', 'geo_lng', 'radio_km',
            ]));

        $items = $query->latest()->get();

        if ($request->filled('q')) {
            $q = mb_strtolower($request->q);
            $items = $items->filter(fn ($i) => str_contains(mb_strtolower($i->titulo . ' ' . $i->descripcion), $q));
        }

        if ($request->filled(['min_lat', 'max_lat', 'min_lng', 'max_lng'])) {
            $minLat = (float) $request->min_lat;
            $maxLat = (float) $request->max_lat;
            $minLng = (float) $request->min_lng;
            $maxLng = (float) $request->max_lng;
            $items = $items->filter(function ($i) use ($minLat, $maxLat, $minLng, $maxLng) {
                $p = array_map('trim', explode(',', (string) $i->coordenadas));
                if (count($p) !== 2 || !is_numeric($p[0]) || !is_numeric($p[1])) {
                    return false;
                }
                $lat = (float) $p[0];
                $lng = (float) $p[1];
                return $lat >= $minLat && $lat <= $maxLat && $lng >= $minLng && $lng <= $maxLng;
            });
        }

        return response()->json($items->map(function ($i) {
            $p = array_map('trim', explode(',', (string) $i->coordenadas));
            return [
                'id' => $i->id,
                'titulo' => $i->titulo,
                'precio_fmt' => $i->precio_formateado,
                'tipo_operacion' => $i->tipo_operacion,
                'tipo_propiedad' => $i->tipo_propiedad,
                'tipo_lbl' => $i->tipo_propiedad ? (\App\Models\Inmueble::tiposPropiedad()[$i->tipo_propiedad] ?? ucfirst($i->tipo_propiedad)) : null,
                'es_comercial' => $i->esComercial(),
                'habitaciones' => $i->habitaciones,
                'ambientes' => $i->ambientes,
                'banos' => $i->banos,
                'cocheras' => $i->cocheras,
                'superficie_cubierta' => $i->superficie_cubierta,
                'ciudad' => $i->ciudad,
                'lat' => isset($p[0]) && is_numeric($p[0]) ? (float) $p[0] : null,
                'lng' => isset($p[1]) && is_numeric($p[1]) ? (float) $p[1] : null,
                'foto' => !empty($i->fotos) ? asset('images/fotosinmuebles/' . $i->fotos[0]) : null,
                'url' => $i->inmobiliaria ? route('propiedad.show', [$i->inmobiliaria->slug, $i->id]) : null,
                'inmobiliaria' => $i->inmobiliaria?->nombre_comercial,
                'es_particular' => (bool) $i->inmobiliaria?->usuario?->hasRole('particular'),
            ];
        })->values());
    }
}
