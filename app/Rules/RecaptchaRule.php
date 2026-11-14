<?php
namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RecaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!RecaptchaService::verify($value, request()->ip())) {
            $fail('La verificación reCAPTCHA falló. Probá de nuevo.');
        }
    }
}
