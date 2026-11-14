<?php
namespace App\Http\Controllers;

use App\Models\Inmobiliaria;
use App\Models\Plan;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class MercadoPagoController extends Controller
{
    public function __construct(protected MercadoPagoService $mp)
    {
    }

    // Crea la preferencia de pago en Mercado Pago y redirige al checkout
    public function pagar(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer|exists:planes,id',
        ]);

        $plan = Plan::where('id', $data['plan_id'])->where('activo', 1)->firstOrFail();
        $inmobiliaria = $request->user()->inmobiliaria;
        abort_if(!$inmobiliaria, 403);

        $preferencia = $this->mp->crearPreferencia([
            'items' => [[
                'title' => 'Plan ' . $plan->nombre . ' - ' . $inmobiliaria->nombre_comercial,
                'quantity' => 1,
                'unit_price' => (float) $plan->precio_mensual,
                'currency_id' => 'ARS',
            ]],
            'external_reference' => 'inmobiliaria_' . $inmobiliaria->id . '_plan_' . $plan->id,
            'back_urls' => [
                'success' => route('mp.retorno', ['estado' => 'success']),
                'failure' => route('mp.retorno', ['estado' => 'failure']),
                'pending' => route('mp.retorno', ['estado' => 'pending']),
            ],
            'auto_return' => 'approved',
            'notification_url' => route('mp.webhook'),
        ]);

        if (!$preferencia || empty($preferencia['init_point'])) {
            return back()->with('error', 'No se pudo generar el pago. Probá de nuevo o contactá al administrador.');
        }

        return redirect()->away($preferencia['init_point']);
    }

    // Vuelta del checkout de Mercado Pago
    public function retorno(Request $request, string $estado)
    {
        $mensajes = [
            'success' => ['success', '¡Pago aprobado! Tu plan se activa en unos minutos.'],
            'pending' => ['warning', 'Tu pago está pendiente de acreditación.'],
            'failure' => ['error', 'El pago no se realizó. Podés intentar de nuevo.'],
        ];
        [$tipo, $texto] = $mensajes[$estado] ?? $mensajes['failure'];

        return redirect()->route('inmobiliaria.perfil')->with($tipo, $texto);
    }

    // Webhook: Mercado Pago avisa los pagos; acá se confirma y se activa el plan
    public function webhook(Request $request)
    {
        $secret = config('services.mercadopago.webhook_secret');
        if ($secret) {
            $sig = $request->header('x-signature', '');
            preg_match('/ts=([^,]+),v1=(.+)/', $sig, $mm);
            $manifest = 'id:' . ($request->input('data.id') ?? '') . ';request-target:' . $request->getRequestUri() . ';ts:' . ($mm[1] ?? '') . ';';
            $expected = hash_hmac('sha256', $manifest, $secret);
            if (!hash_equals($expected, $mm[2] ?? '')) {
                return response()->json(['received' => false], 403);
            }
        }

        $tipo = $request->input('type') ?? $request->input('topic');
        $id = $request->input('data.id') ?? $request->input('id');

        if ($tipo === 'payment' && $id) {
            $pago = $this->mp->obtenerPago((string) $id);

            if ($pago && ($pago['status'] ?? null) === 'approved') {
                $ref = $pago['external_reference'] ?? '';
                if (preg_match('/^inmobiliaria_(\d+)_plan_(\d+)$/', $ref, $m)) {
                    $inmobiliaria = Inmobiliaria::find((int) $m[1]);
                    $plan = Plan::find((int) $m[2]);
                    if ($inmobiliaria && $plan) {
                        $inmobiliaria->plan_id = $plan->id;
                        $inmobiliaria->plan_vence_at = now()->addMonth()->toDateString();
                        if ($inmobiliaria->estado === 'pendiente') {
                            $inmobiliaria->estado = 'activa';
                        }
                        $inmobiliaria->save();
                    }
                }
            }
        }

        return response()->json(['received' => true]);
    }
}
