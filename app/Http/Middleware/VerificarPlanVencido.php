<?php

namespace App\Http\Middleware;

use App\Models\Plan;
use Closure;
use Illuminate\Http\Request;

class VerificarPlanVencido
{
    // Sin cron (hosting compartido): al entrar al panel, si el plan venció,
    // se degrada automáticamente al plan Gratis y se aplican los límites.
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->inmobiliaria) {
            $inmobiliaria = $user->inmobiliaria;

            if ($inmobiliaria->estado === 'activa'
                && $inmobiliaria->plan_vence_at
                && $inmobiliaria->plan_vence_at->isPast()) {

                $gratis = Plan::gratis();

                if ($gratis && (int) $inmobiliaria->plan_id !== (int) $gratis->id) {
                    $inmobiliaria->plan_id = $gratis->id;
                    $inmobiliaria->plan_vence_at = null;
                    $inmobiliaria->save();

                    $resumen = $inmobiliaria->aplicarLimitesDelPlan();

                    $extra = $resumen['despublicados'] > 0
                        ? " Se despublicaron {$resumen['despublicados']} propiedades por el límite del plan."
                        : '';

                    session()->flash('warning', 'Tu suscripción venció y pasaste al plan Gratis.' . $extra . ' Pagá un boleto para volver a tu plan.');
                }
            }
        }

        return $next($request);
    }
}
