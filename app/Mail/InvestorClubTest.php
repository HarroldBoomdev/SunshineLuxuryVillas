<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvestorClubTest extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this
            ->from(
                env('INVESTOR_MAIL_FROM_ADDRESS'),
                env('INVESTOR_MAIL_FROM_NAME', 'Cyprus Investor Club')
            )
            ->subject('Investor Club SMTP Test')
            ->markdown('emails.investor.test');
    }
}
