<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactoMail;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'mensaje' => 'required|string',
        ]);

        $data = $request->only(['nombre', 'email', 'telefono', 'mensaje']);

        Mail::to('scastellano10@gmail.com')->send(new ContactoMail($data));

        return redirect()->route('welcome')->with('success', 'Mensaje enviado con éxito.');
    }
}

