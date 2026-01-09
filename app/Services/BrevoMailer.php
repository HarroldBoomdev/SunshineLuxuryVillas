<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BrevoMailer
{
    protected Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => 'https://api.brevo.com/v3/',
            'timeout'  => 20,
        ]);
    }

    /**
     * Send transactional email via Brevo API (NOT SMTP)
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $html
     * @param string $text
     * @param array  $extra  Optional: replyTo, cc, bcc
     */
    public function send(string $toEmail, string $subject, string $html, string $text, array $extra = []): void
    {
        $apiKey = config('brevo.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('BREVO_API_KEY is missing.');
        }

        $senderEmail = config('brevo.sender_email');
        $senderName  = config('brevo.sender_name', 'Sunshine Luxury Villas');

        if (!$senderEmail) {
            throw new \RuntimeException('BREVO_SENDER_EMAIL is missing.');
        }

        // ✅ Build Brevo payload (IMPORTANT: do NOT include "headers")
        $payload = [
            'sender' => [
                'email' => $senderEmail,
                'name'  => $senderName,
            ],
            'to' => [
                ['email' => $toEmail],
            ],
            'subject'     => $subject,
            'htmlContent' => $html,
            'textContent' => $text,
        ];

        // Optional Reply-To
        if (!empty($extra['replyToEmail'])) {
            $payload['replyTo'] = [
                'email' => $extra['replyToEmail'],
                'name'  => $extra['replyToName'] ?? $senderName,
            ];
        }

        // Optional CC/BCC
        if (!empty($extra['cc']) && is_array($extra['cc'])) {
            $payload['cc'] = array_map(fn($e) => ['email' => $e], $extra['cc']);
        }
        if (!empty($extra['bcc']) && is_array($extra['bcc'])) {
            $payload['bcc'] = array_map(fn($e) => ['email' => $e], $extra['bcc']);
        }

        try {
            $res = $this->http->post('smtp/email', [
                'headers' => [
                    'api-key'       => $apiKey,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
            ]);

            // optional logging (useful while testing)
            Log::info('Brevo send ok', [
                'to'     => $toEmail,
                'status' => $res->getStatusCode(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Brevo send failed', [
                'to'      => $toEmail,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
