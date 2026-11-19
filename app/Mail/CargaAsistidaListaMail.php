<?php

namespace App\Mail;

use App\Models\Inmueble;
use App\Models\Inmobiliaria;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CargaAsistidaListaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inmueble;
    public $inmobiliaria;
    public $url;

    public function __construct(Inmueble $inmueble, Inmobiliaria $inmobiliaria)
    {
        $this->inmueble = $inmueble;
        $this->inmobiliaria = $inmobiliaria;
        $this->url = $inmueble->publicado
            ? route('propiedad.show', [$inmobiliaria->slug, $inmueble->id])
            : route('inmobiliarias.show', $inmobiliaria->slug);
    }

    public function build()
    {
        return $this->view('emails.carga_asistida_lista')
                    ->subject('¡Listo! Ya cargamos tu propiedad: ' . $this->inmueble->titulo)
                    ->with(['inmueble' => $this->inmueble, 'inmobiliaria' => $this->inmobiliaria, 'url' => $this->url]);
    }
}
