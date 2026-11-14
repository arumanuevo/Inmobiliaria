<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
    protected string $base = 'https://api.mercadopago.com';

    protected function token(): ?string
    {
        return config('services.mercadopago.access_token');
    }

    public function crearPreferencia(array $datos): ?array
    {
        if (!$this->token()) {
            return null;
        }

        $response = Http::withToken($this->token())->post($this->base . '/checkout/preferences', $datos);

        return $response->ok() ? $response->json() : null;
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
