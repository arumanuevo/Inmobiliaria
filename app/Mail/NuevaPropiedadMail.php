<?php

namespace App\Mail;

use App\Models\Inmueble;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaPropiedadMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inmueble;
    public $url;

    public function __construct(Inmueble $inmueble)
    {
        $this->inmueble = $inmueble;
        $this->url = $inmueble->inmobiliaria
            ? route('propiedad.show', [$inmueble->inmobiliaria->slug, $inmueble->id])
            : route('inicio');
    }

    public function build()
    {
        return $this->view('emails.nueva_propiedad')
                    ->subject('Nueva propiedad que puede interesarte: ' . $this->inmueble->titulo)
                    ->with(['inmueble' => $this->inmueble, 'url' => $this->url]);
    }
}
