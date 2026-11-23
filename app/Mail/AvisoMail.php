<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AvisoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $titulo,
        public string $cuerpo,
        public string $tipo = 'aviso',
    ) {
    }

    public function build()
    {
        $tipos = \App\Models\Mensaje::tipos();

        return $this->view('emails.aviso')
            ->subject('[' . config('app.name') . '] ' . $this->titulo)
            ->with([
                'titulo' => $this->titulo,
                'cuerpo' => $this->cuerpo,
                'tipoLabel' => $tipos[$this->tipo] ?? ucfirst($this->tipo),
                'urlBandeja' => route('mensajes.mias'),
            ]);
    }
}
