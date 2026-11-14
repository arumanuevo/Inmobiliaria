<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected string $base = 'https://api.mercadopago.com';

    protected function token(): ?string
    {
        return config('services.mercadopago.access_token');
    }

    public function crearPreferencia(array $datos): array
    {
        if (!$this->token()) {
            throw new \RuntimeException('Mercado Pago no está configurado en el servidor (falta el access token).');
        }

        $response = Http::withToken($this->token())->post($this->base . '/checkout/preferences', $datos);
        $body = $response->json() ?: [];

        // Con credenciales TEST la respuesta trae sandbox_init_point; con producción, init_point
        $initPoint = $body['init_point'] ?? $body['sandbox_init_point'] ?? null;

        if (!$response->successful() || !$initPoint) {
            Log::error('MercadoPago crearPreferencia falló', ['status' => $response->status(), 'body' => $body]);
            $mensaje = $body['message'] ?? ($body['error'] ?? ('HTTP ' . $response->status()));
            if (is_array($mensaje)) {
                $mensaje = json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            }
            throw new \RuntimeException('Mercado Pago: ' . $mensaje);
        }

        $body['init_point'] = $initPoint;

        return $body;
    }

    public function obtenerPago(string $id): ?array
    {
        if (!$this->token()) {
            return null;
        }

        $response = Http::withToken($this->token())->get($this->base . '/v1/payments/' . $id);

        return $response->ok() ? $response->json() : null;
    }
}
