<?php

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionReceived extends Mailable
{
    use Queueable, SerializesModels;

    public FormSubmission $submission;

    public function __construct(FormSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function build()
    {
        $s = $this->submission;
        $formKey = $s->form_key ?? 'contact_us';

        // Per-form sender identity
        $fromAddress = match ($formKey) {
            'investor_club' => 'investorclub@sunshineluxuryvillas.com',
            default         => 'enquires@sunshineluxuryvillas.com', // contact_us, property_details, request_callback, etc.
        };

        // Central destination + monitor CCs
        $centralTo = 'ph.sunshineluxuryvillaslimited@gmail.com';
        $alwaysCc  = config('form_inbox._always', []);

        // Reply goes to the visitor
        $replyToEmail = $s->email ?: $fromAddress;
        $replyToName  = $s->name  ?: 'Website Visitor';

        $subject = implode(' • ', array_filter([
            'New Website Enquiry',
            $s->reference,
            $s->name,
        ]));

        return $this->from($fromAddress, 'SLV Estates')
            ->to($centralTo)
            ->cc($alwaysCc)
            ->replyTo($replyToEmail, $replyToName)
            ->subject($subject)
            ->view('emails.form_submission_received', ['submission' => $s]);
    }
}
