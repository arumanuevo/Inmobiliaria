<?php

namespace App\Http\Controllers;

use App\Models\Inmueble;

class FeedController extends Controller
{
    // Propiedades publicadas de inmobiliarias activas (para los feeds XML)
    protected function propiedades()
    {
        return Inmueble::publicados()
            ->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))
            ->with('inmobiliaria')
            ->latest()
            ->get();
    }

    public function generico()
    {
        $propiedades = $this->propiedades();

        return response()->view('feed.generico', compact('propiedades'), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function zonaprop()
    {
        $propiedades = $this->propiedades();

        return response()->view('feed.zonaprop', compact('propiedades'), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function argenprop()
    {
        $propiedades = $this->propiedades();

        return response()->view('feed.argenprop', compact('propiedades'), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
