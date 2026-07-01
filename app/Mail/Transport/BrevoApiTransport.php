<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Http;

class BrevoApiTransport extends AbstractTransport
{
    public function __construct(private string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            return;
        }

        $from = $email->getFrom()[0];

        $payload = [
            'sender' => [
                'email' => $from->getAddress(),
                'name'  => $from->getName() ?: $from->getAddress(),
            ],
            'to' => array_map(fn ($addr) => [
                'email' => $addr->getAddress(),
                'name'  => $addr->getName() ?: $addr->getAddress(),
            ], $email->getTo()),
            'subject'     => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ];

        Http::withHeaders(['api-key' => $this->apiKey])
            ->post('https://api.brevo.com/v3/smtp/email', $payload);
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
