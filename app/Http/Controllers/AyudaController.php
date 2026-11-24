<?php

namespace App\Http\Controllers;

use App\Models\Inmobiliaria;
use App\Models\Mensaje;

class AyudaController extends Controller
{
    /**
     * Centro de ayuda del sistema (visible para todos menos el superadmin,
     * que ya administra todo desde /admin). Reúne guía paso a paso, estado
     * del plan con barras de uso, sección por rol y FAQ.
     */
    public function index()
    {
        $user = auth()->user();
        $inmobiliaria = $user->inmobiliaria;

        $cuenta = null;
        if ($inmobiliaria) {
            $publicadas = $inmobiliaria->inmuebles()->where('publicado', 1)->count();
            $destacadas = $inmobiliaria->inmuebles()->where('destacado', 1)->count();
            $limiteInmuebles = $inmobiliaria->limiteInmuebles();
            $limiteDestacados = $inmobiliaria->limiteDestacados();
            $cuenta = [
                'inmobiliaria' => $inmobiliaria,
                'publicadas' => $publicadas,
                'limite_inmuebles' => $limiteInmuebles,
                'pct_pubs' => min(100, (int) round(100 * $publicadas / max(1, $limiteInmuebles))),
                'destacadas' => $destacadas,
                'limite_destacados' => $limiteDestacados,
                'pct_dest' => min(100, (int) round(100 * $destacadas / max(1, $limiteDestacados))),
                'fotos_limite' => (int) ($inmobiliaria->plan->limite_fotos ?? 5),
                'verificada_col' => Inmobiliaria::columnaExiste('verificada'),
                'boletos_pendientes' => $inmobiliaria->boletos()->where('estado', 'pendiente')->count(),
            ];
        }

        $noLeidos = Mensaje::where('usuario_id', $user->id)->whereNull('leido_at')->count();

        $esParticular = $user->hasRole('particular');
        $esInmobiliaria = $user->hasRole('inmobiliaria');
        $emailVerificado = $user->hasVerifiedEmail();

        return view('ayuda', compact('cuenta', 'noLeidos', 'esParticular', 'esInmobiliaria', 'emailVerificado'));
    }
}
