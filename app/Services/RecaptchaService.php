<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public static function verify(?string $token, ?string $ip = null): bool
    {
        $secret = config('services.recaptcha.secret_key');

        if (!$secret) {
            return true; // sin configurar (desarrollo local), no bloquear
        }

        if (!$token) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip,
        ]);

        return $response->ok() && (bool) ($response->json('success') ?? false);
    }
}
