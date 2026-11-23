<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $motivo = !empty($this->data['motivo']) ? ' (' . $this->data['motivo'] . ')' : '';

        return $this->view('emails.contacto')
                    ->subject('Nuevo contacto' . $motivo . ' - ' . config('app.name'))
                    ->with(['data' => $this->data]);
    }
}
