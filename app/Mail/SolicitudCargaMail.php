<?php

namespace App\Mail;

use App\Models\SolicitudCarga;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudCargaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    public function __construct(SolicitudCarga $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function build()
    {
        $nombre = $this->solicitud->inmobiliaria->nombre_comercial ?? 'Inmobiliaria';

        return $this->view('emails.solicitud_carga')
                    ->subject('Nueva solicitud de carga asistida - ' . $nombre)
                    ->with(['solicitud' => $this->solicitud]);
    }
}
