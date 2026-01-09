<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PropertyRecommendationsMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public array $properties;

    public function __construct(string $clientName, array $properties)
    {
        $this->clientName = $clientName;
        $this->properties = $properties;
    }

    public function build()
    {
        return $this->subject('Sunshine Luxury Villas — Property Recommendations')
            ->view('emails.property-recommendations');
    }
}
