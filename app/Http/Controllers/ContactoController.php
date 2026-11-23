<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactoMail;
use App\Models\Contacto;
use App\Models\Inmobiliaria;
use App\Rules\RecaptchaRule;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'mensaje' => 'required|string',
            'inmobiliaria_id' => 'nullable|integer',
            'inmueble_id' => 'nullable|integer',
            'motivo' => 'nullable|string|max:60',
            'g-recaptcha-response' => ['nullable', new RecaptchaRule],
        ]);

        // El motivo solo viaja en el email, no se guarda en la tabla contactos
        $motivo = $data['motivo'] ?? null;
        unset($data['motivo']);

        Contacto::create($data);

        if ($motivo) {
            $data['motivo'] = $motivo;
        }

        $inmobiliaria = !empty($data['inmobiliaria_id'])
            ? Inmobiliaria::find($data['inmobiliaria_id'])
            : null;

        if ($inmobiliaria) {
            $data['inmobiliaria_nombre'] = $inmobiliaria->nombre_comercial;
        }

        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $destinatario = $inmobiliaria?->email ?: (config('mail.from.address') ?: 'contacto@' . $host);

        try {
            Mail::to($destinatario)->send(new ContactoMail($data));
        } catch (\Throwable $e) {
            // No romper el formulario si el SMTP falla; queda asentado en el log
            \Illuminate\Support\Facades\Log::warning('Mail de contacto no enviado a ' . $destinatario . ': ' . $e->getMessage());
        }

        return redirect()->route('welcome')->with('success', 'Mensaje enviado con éxito.');
    }
}

