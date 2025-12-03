<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\FormSubmission;
use App\Services\BrevoMail;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $q = FormSubmission::query()->latest();

        if ($request->filled('form_key')) {
            $q->where('form_key', $request->string('form_key'));
        }

        if ($request->filled('type')) {
            $type    = $request->string('type');          // e.g. investor-club
            $formKey = str_replace('-', '_', $type);      // investor_club

            $q->where(function ($qq) use ($type, $formKey) {
                $qq->where('type', $type)
                   ->orWhere('form_key', $formKey);
            });
        }

        return response()->json([
            'ok'   => true,
            'data' => $q->paginate(50),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'form_key'  => 'required|string|max:100',
            'name'      => 'nullable|string|max:255',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'payload'   => 'nullable|array',
            'message'   => 'nullable|string',
            'enquiry'   => 'nullable|string',
            'url'       => 'nullable|string',
        ]);

        // Merge loose fields into payload
        $payload = $data['payload'] ?? [];

        if (!isset($payload['message']) && $request->filled('message')) {
            $payload['message'] = (string) $request->input('message');
        }

        if (!isset($payload['message']) && !empty($data['enquiry'])) {
            $payload['message'] = $data['enquiry'];
        }

        if (!isset($payload['url']) && !empty($data['url'])) {
            $payload['url'] = $data['url'];
        }

        if (!isset($payload['page_url']) && $request->filled('page_url')) {
            $payload['page_url'] = $request->string('page_url');
        }

        if (!isset($payload['referrer']) && $request->filled('referrer')) {
            $payload['referrer'] = $request->string('referrer');
        }

        // Map to hyphenated type (what the Inbox UI uses)
        $type = match ($data['form_key']) {
            'property_details' => 'property-details',
            'investor_club'    => 'investor-club',
            'sell_with_us'     => 'sell-with-us',
            'contact_us'       => 'contact-us',
            'affiliate'        => 'affiliate-page',
            'request_callback' => 'request-callback',
            default            => str_replace('_', '-', $data['form_key']),
        };

        $submission = FormSubmission::create([
            'form_key'   => $data['form_key'],
            'type'       => $type,
            'name'       => $data['name']      ?? null,
            'email'      => $data['email']     ?? null,
            'phone'      => $data['phone']     ?? null,
            'reference'  => $data['reference'] ?? null,
            'payload'    => $payload,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Notify SLV team via Brevo API
        $this->sendNotificationEmail($submission);

        // Auto-reply only for Investor Club sign-ups
        if (
            $submission->form_key === 'investor_club' &&
            filter_var($submission->email, FILTER_VALIDATE_EMAIL)
        ) {
            $name = $submission->name ?: 'Investor';

            $subject = 'Welcome to the Sunshine Luxury Villas Investor Club';

            $html  = '<p>Dear ' . e($name) . ',</p>';
            $html .= '<p>Thank you for joining the <strong>Sunshine Luxury Villas Investor Club</strong>. ';
            $html .= 'We have received your details and a member of our team will be in touch shortly with more information ';
            $html .= 'about our latest opportunities.</p>';
            $html .= '<p>If you have any urgent questions, simply reply to this email.</p>';
            $html .= '<p>Best regards,<br>Sunshine Luxury Villas Team</p>';

            $ok = BrevoMail::send(
                [$submission->email],
                $subject,
                $html
            );

            if (! $ok) {
                Log::warning('Investor welcome mail failed via Brevo', [
                    'submission_id' => $submission->id,
                ]);
            }
        }

        return response()->json(['ok' => true, 'id' => $submission->id], 201);
    }

    /**
     * Send notification email to SLV team based on config/form_inbox.php
     */
    protected function sendNotificationEmail(FormSubmission $submission): void
    {
        $routes = config('form_inbox', []);

        if (empty($routes)) {
            Log::warning('Inbox notification: config/form_inbox.php missing or empty');
            return;
        }

        $key    = $submission->form_key; // e.g. investor_club
        $route  = $routes[$key] ?? ($routes['_default'] ?? null);
        $always = $routes['_always'] ?? [];

        if (!$route) {
            Log::warning('Inbox notification: no route for form_key', [
                'form_key' => $key,
            ]);
            return;
        }

        $to  = array_values(array_unique($route['to']  ?? []));
        $cc  = array_values(array_unique(array_merge($route['cc'] ?? [], $always)));
        $bcc = $route['bcc'] ?? [];

        if (empty($to) && empty($cc) && empty($bcc)) {
            Log::warning('Inbox notification: no recipients for form_key', [
                'form_key' => $key,
            ]);
            return;
        }

        $payload  = $submission->payload ?? [];
        $msg      = $payload['message']   ?? null;
        $pageUrl  = $payload['page_url']  ?? ($payload['url'] ?? null);
        $referrer = $payload['referrer']  ?? null;

        $subject = 'New ' . $submission->type . ' enquiry from ' . ($submission->name ?: 'Website visitor');

        $html  = '<p>You have a new enquiry from the website.</p>';
        $html .= '<p><strong>Name:</strong> '  . e($submission->name ?? '')  . '<br>';
        $html .=     '<strong>Email:</strong> ' . e($submission->email ?? '') . '<br>';
        $html .=     '<strong>Phone:</strong> ' . e($submission->phone ?? '') . '<br>';
        $html .=     '<strong>Type:</strong> '  . e($submission->form_key)    . '</p>';

        if ($submission->reference) {
            $html .= '<p><strong>Property Reference:</strong> ' . e($submission->reference) . '</p>';
        }

        if ($msg) {
            $html .= '<p><strong>Message:</strong><br>' . nl2br(e($msg)) . '</p>';
        }

        if ($pageUrl) {
            $html .= '<p><strong>Page URL:</strong> ' . e($pageUrl) . '</p>';
        }

        if ($referrer) {
            $html .= '<p><strong>Referrer:</strong> ' . e($referrer) . '</p>';
        }

        $html .= '<hr><p>IP: ' . e($submission->ip ?? '') . '<br>';
        $html .= 'User Agent: ' . e($submission->user_agent ?? '') . '</p>';

        $ok = BrevoMail::send($to, $subject, $html, $cc, $bcc);

        if (! $ok) {
            Log::error('Inbox notification: Brevo send failed', [
                'submission_id' => $submission->id,
                'form_key'      => $submission->form_key,
            ]);
        }
    }
}
