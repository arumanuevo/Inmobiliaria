<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.inmobiliarias');
        }

        if ($user->hasAnyRole(['inmobiliaria', 'particular'])) {
            $inmobiliaria = $user->inmobiliaria;
            $cuenta = null;
            if ($inmobiliaria) {
                $cuenta = [
                    'inmobiliaria' => $inmobiliaria,
                    'publicadas' => $inmobiliaria->inmuebles()->where('publicado', 1)->count(),
                    'total' => $inmobiliaria->inmuebles()->count(),
                    'destacadas' => $inmobiliaria->inmuebles()->where('destacado', 1)->count(),
                    'limite_inmuebles' => $inmobiliaria->limiteInmuebles(),
                    'limite_destacados' => $inmobiliaria->limiteDestacados(),
                    'boletos_pendientes' => $inmobiliaria->boletos()->where('estado', 'pendiente')->count(),
                ];
            }
            return view('home', compact('cuenta'));
        }

        return view('home');
    }
}
