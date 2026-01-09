<?php

namespace App\Services;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;

class BrevoMailer
{
    public function send(string $toEmail, ?string $toName, string $subject, string $html): void
    {
        $apiKey = config('brevo.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('BREVO_API_KEY is missing in .env');
        }

        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', $apiKey);

        $api = new TransactionalEmailsApi(
            new GuzzleClient(),
            $config
        );

        $email = new SendSmtpEmail([
            'subject' => $subject,
            'htmlContent' => $html,
            'sender' => [
                'email' => config('brevo.sender_email'),
                'name'  => config('brevo.sender_name'),
            ],
            'to' => [[
                'email' => $toEmail,
                'name'  => $toName ?: $toEmail,
            ]],
        ]);

        $api->sendTransacEmail($email);
    }
}
