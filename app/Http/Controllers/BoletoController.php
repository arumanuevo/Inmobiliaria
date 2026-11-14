<?php
namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class BoletoController extends Controller
{
    public function __construct(protected MercadoPagoService $mp)
    {
    }

    // Listado de boletos de la propia inmobiliaria
    public function mios()
    {
        $inmobiliaria = auth()->user()->inmobiliaria;
        abort_if(!$inmobiliaria, 403);

        $boletos = $inmobiliaria->boletos()->with('plan')->latest()->paginate(15);

        return view('inmobiliarias.boletos', compact('boletos'));
    }

    // Inicia el pago de un boleto con Mercado Pago
    public function pagar(Request $request, Boleto $boleto)
    {
        $inmobiliaria = $request->user()->inmobiliaria;
        abort_if(!$inmobiliaria || (int) $boleto->inmobiliaria_id !== (int) $inmobiliaria->id, 403);
        abort_if($boleto->estado !== 'pendiente', 403, 'Este boleto ya no está pendiente de pago.');
        abort_if((float) $boleto->monto <= 0, 403, 'El monto del boleto debe ser mayor a cero.');

        try {
            $preferencia = $this->mp->crearPreferencia([
                'items' => [[
                    'title' => 'Boleto #' . $boleto->id . ' - ' . $boleto->concepto . ' - ' . $inmobiliaria->nombre_comercial,
                    'quantity' => 1,
                    'unit_price' => (float) $boleto->monto,
                    'currency_id' => 'ARS',
                ]],
                'external_reference' => 'boleto_' . $boleto->id,
                'back_urls' => [
                    'success' => route('mp.retorno', ['estado' => 'success']),
                    'failure' => route('mp.retorno', ['estado' => 'failure']),
                    'pending' => route('mp.retorno', ['estado' => 'pending']),
                ],
                'auto_return' => 'approved',
                'notification_url' => route('mp.webhook'),
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', 'No se pudo generar el pago. ' . $e->getMessage());
        }

        $boleto->mp_preference_id = $preferencia['id'] ?? null;
        $boleto->save();

        return redirect()->away($preferencia['init_point']);
    }
}
