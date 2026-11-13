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

        return view('inmuebles.search', compact('inmobiliarias'));
    }
}
