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
}
