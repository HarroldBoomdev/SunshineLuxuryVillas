<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvestorWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    public function __construct(?string $name = '')
    {
        $this->name = trim((string) $name);
    }

    public function build()
    {
        // Sender (override per-mailer “from” just in case)
        $fromAddress = config('mail.mailers.investorclub.from.address')
                       ?? env('INVESTOR_MAIL_FROM_ADDRESS');
        $fromName    = config('mail.mailers.investorclub.from.name')
                       ?? env('INVESTOR_MAIL_FROM_NAME', 'Cyprus Investor Club');

        return $this
            ->from($fromAddress, $fromName)
            ->replyTo($fromAddress, $fromName)
            ->subject('Welcome to The Cyprus Investor Club!')
            ->view('mail.investor_welcome');
    }
}
