<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwilioService;

class MessageController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function showForm()
    {
        return view('send');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'to' => 'required',
            'message' => 'required',
        ]);

        $to = $request->input('to');
        $message = $request->input('message');

        $response = $this->twilioService->sendWhatsAppMessage($to, $message);

        return redirect()->back()->with('success', 'Mensaje enviado con éxito!');
    }
}
