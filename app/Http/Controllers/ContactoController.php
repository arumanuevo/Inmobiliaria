<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactoMail;
use App\Models\Contacto;
use App\Models\Inmobiliaria;
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
        ]);

        Contacto::create($data);

        $inmobiliaria = !empty($data['inmobiliaria_id'])
            ? Inmobiliaria::find($data['inmobiliaria_id'])
            : null;

        if ($inmobiliaria) {
            $data['inmobiliaria_nombre'] = $inmobiliaria->nombre_comercial;
        }

        $destinatario = $inmobiliaria?->email ?: (config('mail.from.address') ?: 'contacto@gertech-arumasoft.com');

        Mail::to($destinatario)->send(new ContactoMail($data));

        return redirect()->route('welcome')->with('success', 'Mensaje enviado con éxito.');
    }
}

