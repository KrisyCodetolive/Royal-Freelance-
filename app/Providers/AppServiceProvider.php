<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        Mail::extend('brevo', function () {
            $apiKey = env('BREVO_API_KEY');

            return new class($apiKey) extends AbstractTransport {
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
                    $response = Http::withHeaders(['api-key' => $this->apiKey])
                        ->post('https://api.brevo.com/v3/smtp/email', [
                            'sender'      => ['email' => $from->getAddress(), 'name' => $from->getName() ?: $from->getAddress()],
                            'to'          => array_map(fn ($a) => ['email' => $a->getAddress(), 'name' => $a->getName() ?: $a->getAddress()], $email->getTo()),
                            'subject'     => $email->getSubject(),
                            'htmlContent' => $email->getHtmlBody(),
                            'textContent' => $email->getTextBody(),
                        ]);

                    if ($response->failed()) {
                        \Illuminate\Support\Facades\Log::error('[BREVO] Échec envoi email', [
                            'status' => $response->status(),
                            'body'   => $response->body(),
                        ]);
                        throw new \Exception('[BREVO] ' . $response->body());
                    }
                }

                public function __toString(): string
                {
                    return 'brevo';
                }
            };
        });
    }
}
