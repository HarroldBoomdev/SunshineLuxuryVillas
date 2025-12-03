<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMail
{
    /**
     * Send an email via Brevo HTTP API.
     *
     * @param  array       $to       Array of recipient emails
     * @param  string      $subject
     * @param  string      $html     HTML body
     * @param  array       $cc       Array of CC emails
     * @param  array       $bcc      Array of BCC emails
     * @return bool
     */
    public static function send(array $to, string $subject, string $html, array $cc = [], array $bcc = []): bool
    {
        $apiKey = config('services.brevo.key');

        if (!$apiKey) {
            Log::warning('BrevoMail: API key missing');
            return false;
        }

        $senderEmail = config('services.brevo.sender_email');
        $senderName  = config('services.brevo.sender_name');

        if (!$senderEmail) {
            Log::warning('BrevoMail: sender email missing');
            return false;
        }

        // Build recipients
        $toPayload = collect($to)
            ->filter()
            ->unique()
            ->map(fn ($email) => ['email' => $email])
            ->values()
            ->all();

        $ccPayload = collect($cc)
            ->filter()
            ->unique()
            ->map(fn ($email) => ['email' => $email])
            ->values()
            ->all();

        $bccPayload = collect($bcc)
            ->filter()
            ->unique()
            ->map(fn ($email) => ['email' => $email])
            ->values()
            ->all();

        if (empty($toPayload) && empty($ccPayload) && empty($bccPayload)) {
            Log::warning('BrevoMail: no recipients supplied');
            return false;
        }

        $payload = [
            'sender' => [
                'email' => $senderEmail,
                'name'  => $senderName,
            ],
            'to'          => $toPayload,
            'subject'     => $subject,
            'htmlContent' => $html,
        ];

        if (!empty($ccPayload)) {
            $payload['cc'] = $ccPayload;
        }

        if (!empty($bccPayload)) {
            $payload['bcc'] = $bccPayload;
        }

        try {
            $response = Http::withHeaders([
                    'accept'       => 'application/json',
                    'content-type' => 'application/json',
                    'api-key'      => $apiKey,
                ])
                ->post('https://api.brevo.com/v3/smtp/email', $payload);

            if (! $response->successful()) {
                Log::error('BrevoMail: send failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('BrevoMail: exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
