<?php

namespace App\Http\Controllers;

use App\Mail\SolicitudCargaMail;
use App\Models\SolicitudCarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SolicitudCargaController extends Controller
{
    // Vista del cliente (inmobiliaria/particular): explicación + form + historial de solicitudes
    public function mis()
    {
        $inmobiliaria = auth()->user()->inmobiliaria;
        abort_if(!$inmobiliaria, 403, 'Aún no tenés un perfil de inmobiliaria.');

        $solicitudes = SolicitudCarga::where('inmobiliaria_id', $inmobiliaria->id)
            ->with('inmueble')
            ->latest()
            ->get();

        return view('solicitudes.mis', compact('solicitudes', 'inmobiliaria'));
    }

    // El cliente envía una solicitud de carga asistida; se avisa por email al admin
    public function store(Request $request)
    {
        $inmobiliaria = auth()->user()->inmobiliaria;
        abort_if(!$inmobiliaria, 403, 'Aún no tenés un perfil de inmobiliaria.');

        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'detalle' => 'required|string|max:2000',
        ]);

        $solicitud = SolicitudCarga::create([
            'inmobiliaria_id' => $inmobiliaria->id,
            'titulo' => $data['titulo'] ?? null,
            'detalle' => $data['detalle'],
            'estado' => 'pendiente',
        ]);

        try {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
            $admin = config('app.superadmin_email') ?: (config('mail.from.address') ?: 'contacto@' . $host);
            Mail::to($admin)->send(new SolicitudCargaMail($solicitud));
        } catch (\Throwable $e) {
            Log::warning('Mail de solicitud de carga no enviado: ' . $e->getMessage());
        }

        return back()->with('success', '¡Solicitud enviada! Nuestro equipo arma la publicación y te avisa por email apenas esté online.');
    }
}
