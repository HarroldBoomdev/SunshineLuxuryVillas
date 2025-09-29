<?php

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormSubmissionReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FormSubmission $submission) {}

    public function envelope(): Envelope
    {
        $subject = 'New website submission: ' . str_replace('_', ' ', $this->submission->form_key);

        return new Envelope(
            subject: $subject,
            replyTo: $this->submission->email
                ? [new Address($this->submission->email, $this->submission->name)]
                : []
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.form_submission'
        );
    }
    public function build()
    {
        /** @var \App\Models\FormSubmission $s */
        $s = $this->submission ?? $this->data['submission'] ?? null; // adjust if your ctor differs
        $formKey = $s?->form_key ?? 'contact_us';

        // Pick the sender identity per form
        $fromAddress = match ($formKey) {
            'investor_club'    => 'investorclub@sunshineluxuryvillas.com',
            default            => 'enquires@sunshineluxuryvillas.com', // contact_us, property_details, request_callback, etc.
        };

        // If the visitor provided an email, replying should go to them
        $replyToEmail = $s?->email ?: $fromAddress;
        $replyToName  = $s?->name  ?: 'Website Visitor';

        // Subject
        $subject = implode(' • ', array_filter([
            'New Website Enquiry',
            $s?->reference,
            $s?->name,
        ]));

        return $this->from($fromAddress, 'SLV Estates')
            ->replyTo($replyToEmail, $replyToName)
            ->subject($subject)
            ->view('emails.form_submission_received', [
                'submission' => $s
            ]);

    }


}
