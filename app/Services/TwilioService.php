<?php
namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $accountSid = env('TWILIO_ACCOUNT_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $this->client = new Client($accountSid, $authToken);
    }

    public function sendWhatsAppMessage($to, $message)
    {
        $from = 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER');
        $to = 'whatsapp:' . $to;

        return $this->client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }
}
