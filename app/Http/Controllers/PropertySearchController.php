<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inmobiliaria;
use App\Models\Inmueble;

class PropertySearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Inmueble::publicados()
            ->whereHas('inmobiliaria', function ($q) {
                $q->where('estado', 'activa');
            })
            ->with('inmobiliaria');

        if ($request->filled('inmobiliaria')) {
            $query->whereHas('inmobiliaria', function ($q) use ($request) {
                $q->where('slug', $request->inmobiliaria);
            });
        }

        if ($request->has('tipo_operacion') && !empty($request->tipo_operacion)) {
            $query->where('tipo_operacion', $request->tipo_operacion);
        }

        if ($request->filled('tipo_propiedad')) {
            $query->where('tipo_propiedad', $request->tipo_propiedad);
        }

        if ($request->filled('moneda')) {
            $query->where('moneda', $request->moneda);
        }

        if ($request->filled('ambientes_min') && is_numeric($request->ambientes_min)) {
            $query->where('ambientes', '>=', (int) $request->ambientes_min);
        }

        if ($request->filled('cocheras_min') && is_numeric($request->cocheras_min)) {
            $query->where('cocheras', '>=', (int) $request->cocheras_min);
        }

        if ($request->has('precio_min') && is_numeric($request->precio_min)) {
            $query->where('precio', '>=', $request->precio_min);
        }

        if ($request->has('precio_max') && is_numeric($request->precio_max)) {
            $query->where('precio', '<=', $request->precio_max);
        }

        if ($request->has('habitaciones') && is_numeric($request->habitaciones)) {
            $query->where('habitaciones', $request->habitaciones);
        }

        if ($request->has('banos') && is_numeric($request->banos)) {
            $query->where('banos', $request->banos);
        }

        if ($request->has('superficie_min') && is_numeric($request->superficie_min)) {
            $query->where('superficie_cubierta', '>=', $request->superficie_min);
        }

        if ($request->has('superficie_max') && is_numeric($request->superficie_max)) {
            $query->where('superficie_cubierta', '<=', $request->superficie_max);
        }

        if ($request->has('ciudad') && !empty($request->ciudad)) {
            $query->where('ciudad', 'like', '%' . $request->ciudad . '%');
        }

        $properties = $query->paginate(3);

        return view('inmuebles.partials.property_results', compact('properties'));
    }

    public function index()
    {
        $inmobiliarias = Inmobiliaria::activas()->orderBy('nombre_comercial')->get();
        $tipos = Inmueble::tiposPropiedad();

        return view('inmuebles.search', compact('inmobiliarias', 'tipos'));
    }

    // Vista de mapa con marcadores (búsqueda por área visible)
    public function mapa()
    {
        $tipos = Inmueble::tiposPropiedad();

        return view('inmuebles.mapa', compact('tipos'));
    }

    // JSON de propiedades publicadas con coordenadas, filtradas por bounds del mapa y filtros
    public function apiMapa(Request $request)
    {
        $items = Inmueble::publicados()
            ->whereHas('inmobiliaria', function ($q) {
                $q->where('estado', 'activa');
            })
            ->whereNotNull('coordenadas')
            ->where('coordenadas', '!=', '')
            ->with('inmobiliaria')
            ->latest()
            ->get();

        if ($request->filled('tipo_operacion')) {
            $items = $items->where('tipo_operacion', $request->tipo_operacion);
        }
        if ($request->filled('tipo_propiedad')) {
            $items = $items->where('tipo_propiedad', $request->tipo_propiedad);
        }
        if ($request->filled('moneda')) {
            $items = $items->where('moneda', $request->moneda);
        }
        if (is_numeric($request->precio_min)) {
            $items = $items->where('precio', '>=', (float) $request->precio_min);
        }
        if (is_numeric($request->precio_max)) {
            $items = $items->where('precio', '<=', (float) $request->precio_max);
        }
        if ($request->filled('ciudad')) {
            $items = $items->filter(fn ($i) => str_contains(mb_strtolower((string) $i->ciudad), mb_strtolower($request->ciudad)));
        }
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
                'habitaciones' => $i->habitaciones,
                'ambientes' => $i->ambientes,
                'banos' => $i->banos,
                'cocheras' => $i->cocheras,
                'superficie_cubierta' => $i->superficie_cubierta,
                'ciudad' => $i->ciudad,
                'lat' => isset($p[0]) && is_numeric($p[0]) ? (float) $p[0] : null,
                'lng' => isset($p[1]) && is_numeric($p[1]) ? (float) $p[1] : null,
                'foto' => !empty($i->fotos) ? asset('images/fotosinmuebles/' . $i->fotos[0]) : null,
                'url' => $i->inmobiliaria ? route('inmobiliarias.show', $i->inmobiliaria->slug) : null,
                'inmobiliaria' => $i->inmobiliaria?->nombre_comercial,
            ];
        })->values());
    }
}
