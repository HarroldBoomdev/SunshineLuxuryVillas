<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Services\BrevoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $q = FormSubmission::query()->latest();

        if ($request->filled('form_key')) {
            $q->where('form_key', $request->string('form_key'));
        }

        if ($request->filled('type')) {
            $type    = $request->string('type');     // e.g. investor-club
            $formKey = str_replace('-', '_', $type); // investor_club

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
            'page_url'  => 'nullable|string',
            'referrer'  => 'nullable|string',
        ]);

        // ---- Build payload consistently (message/url/page_url/referrer) ----
        $payload = $data['payload'] ?? [];

        // message
        if (!isset($payload['message']) && $request->filled('message')) {
            $payload['message'] = (string) $request->input('message');
        }
        if (!isset($payload['message']) && !empty($data['enquiry'])) {
            $payload['message'] = $data['enquiry'];
        }

        // url / page_url / referrer
        if (!isset($payload['url']) && !empty($data['url'])) {
            $payload['url'] = $data['url'];
        }
        if (!isset($payload['page_url']) && !empty($data['page_url'])) {
            $payload['page_url'] = $data['page_url'];
        }
        if (!isset($payload['referrer']) && !empty($data['referrer'])) {
            $payload['referrer'] = $data['referrer'];
        }

        // ---- Map to the hyphenated type used by the Inbox UI ----
        $type = match ($data['form_key']) {
            'property_details' => 'property-details',
            'investor_club'    => 'investor-club',
            'sell_with_us'     => 'sell-with-us',
            'contact_us'       => 'contact-us',
            'affiliate'        => 'affiliate-page',
            'request_callback' => 'request-callback',
            'subscribe'        => 'subscribe',
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

        Log::info('InboxController@store created submission', [
            'id' => $submission->id,
            'form_key' => $submission->form_key,
            'type' => $submission->type,
            'email' => $submission->email,
        ]);


        // 1) Team notification (routes via config/form_inbox.php)
        $this->sendNotificationEmail($submission);

        // 2) Auto-replies (Investor Club + Sell With Us)
        $this->sendAutoReplies($submission);

        return response()->json(['ok' => true, 'id' => $submission->id], 201);
    }

    /**
     * Send notification email to SLV team based on config/form_inbox.php
     */
    protected function sendNotificationEmail(FormSubmission $submission): void
    {
        // ✅ DEBUG: prove this method is reached
        Log::info('sendNotificationEmail hit', [
            'id'       => $submission->id,
            'form_key' => $submission->form_key,
        ]);

        $routes = config('form_inbox', []);

        if (empty($routes) || !is_array($routes)) {
            Log::warning('Inbox notification: config/form_inbox.php missing or empty');
            return;
        }

        $key    = (string) $submission->form_key; // e.g. sell_with_us
        $route  = $routes[$key] ?? ($routes['_default'] ?? null);
        $always = $routes['_always'] ?? [];

        if (!$route || !is_array($route)) {
            Log::warning('Inbox notification: no route for form_key', ['form_key' => $key]);
            return;
        }

        // ✅ Build recipients
        $to  = array_values(array_unique($route['to']  ?? []));
        $cc  = array_values(array_unique($route['cc']  ?? []));
        // ✅ _always -> BCC (best for "always notify")
        $bcc = array_values(array_unique(array_merge($route['bcc'] ?? [], $always)));

        // Remove empties & non-strings
        $to  = array_values(array_filter($to,  fn ($v) => is_string($v) && trim($v) !== ''));
        $cc  = array_values(array_filter($cc,  fn ($v) => is_string($v) && trim($v) !== ''));
        $bcc = array_values(array_filter($bcc, fn ($v) => is_string($v) && trim($v) !== ''));

        if (empty($to) && empty($cc) && empty($bcc)) {
            Log::warning('Inbox notification: no recipients for form_key', ['form_key' => $key]);
            return;
        }

        // ✅ Extract message + urls
        $payload  = $submission->payload ?? [];
        $msg      = $payload['message'] ?? null;
        $pageUrl  = $payload['page_url'] ?? ($payload['url'] ?? null);
        $referrer = $payload['referrer'] ?? null;

        $isSell = ($submission->form_key === 'sell_with_us');

        // ✅ Subject
        $subject = $isSell
            ? '[SLV] Sell With Us enquiry — ' . ($submission->name ?: 'Website visitor')
            : '[SLV] New ' . ($submission->type ?: $submission->form_key) . ' enquiry — ' . ($submission->name ?: 'Website visitor');

        // ✅ Common rows (table)
        $rows = [
            'Name'               => $this->e($submission->name ?? '—'),
            'Email'              => $this->e($submission->email ?? '—'),
            'Phone'              => $this->e($submission->phone ?? '—'),
            'Form key'           => $this->e($submission->form_key),
            'Type'               => $this->e($submission->type),
            'Property reference' => $submission->reference ? $this->e($submission->reference) : null,
        ];

        if ($pageUrl) {
            $safeUrl = $this->e($pageUrl);
            $rows['Page URL'] = '<a href="'.$safeUrl.'" target="_blank" rel="noreferrer noopener">'.$safeUrl.'</a>';
        }
        if ($referrer) {
            $rows['Referrer'] = $this->e($referrer);
        }

        // ✅ Body HTML
        if ($isSell) {
            // A) Sell With Us — polished “card” layout
            $body  = '<div style="font-weight:800;font-size:18px;margin-bottom:2px;">New Sell With Us enquiry</div>';
            $body .= '<div style="color:#6b7280;font-size:13px;margin-bottom:10px;">A property owner submitted a selling enquiry via the website.</div>';

            $body .= $this->kvTable($rows);

            if (!empty($msg)) {
                $body .= '
                    <div style="margin-top:14px;font-weight:800;">Message</div>
                    <div style="margin-top:6px;padding:12px;border:1px solid #e5e7eb;border-radius:10px;background:#f9fafb;white-space:pre-line;">'
                    . nl2br($this->e($msg)) .
                    '</div>
                ';
            }

            $body .= '
                <div style="margin-top:14px;font-size:12px;color:#6b7280;">
                    IP: '.$this->e($submission->ip ?? '').'<br>
                    User Agent: '.$this->e($submission->user_agent ?? '').'
                </div>
            ';

            $html = $this->mailWrap('Sell With Us', $body);
        } else {
            // B) Other forms — stable/simple (keep working)
            $html  = '<p>You have a new enquiry from the website.</p>';
            $html .= '<table cellpadding="6" cellspacing="0" border="0" style="border-collapse:collapse;font-family:Arial,sans-serif;">';

            $html .= '<tr><td style="font-weight:bold;">Name</td><td>'  . e($submission->name ?? '—')  . '</td></tr>';
            $html .= '<tr><td style="font-weight:bold;">Email</td><td>' . e($submission->email ?? '—') . '</td></tr>';
            $html .= '<tr><td style="font-weight:bold;">Phone</td><td>' . e($submission->phone ?? '—') . '</td></tr>';
            $html .= '<tr><td style="font-weight:bold;">Type</td><td>'  . e($submission->type ?? '—') . ' (' . e($submission->form_key) . ')' . '</td></tr>';

            if (!empty($submission->reference)) {
                $html .= '<tr><td style="font-weight:bold;">Property Ref</td><td>' . e($submission->reference) . '</td></tr>';
            }
            if (!empty($pageUrl)) {
                $html .= '<tr><td style="font-weight:bold;">Page URL</td><td><a href="' . e($pageUrl) . '" target="_blank" rel="noreferrer noopener">' . e($pageUrl) . '</a></td></tr>';
            }
            if (!empty($referrer)) {
                $html .= '<tr><td style="font-weight:bold;">Referrer</td><td>' . e($referrer) . '</td></tr>';
            }

            $html .= '</table>';

            if (!empty($msg)) {
                $html .= '<p><strong>Message:</strong><br>' . nl2br(e($msg)) . '</p>';
            }

            $html .= '<hr style="border:none;border-top:1px solid #eee;margin:18px 0;">';
            $html .= '<p style="font-size:12px;color:#666;">'
                . 'IP: ' . e($submission->ip ?? '') . '<br>'
                . 'User Agent: ' . e($submission->user_agent ?? '')
                . '</p>';
        }

        // ✅ Send via Brevo
        $ok = BrevoMail::send($to, $subject, $html, $cc, $bcc);

        // ✅ DEBUG: show recipients + result
        Log::info('BrevoMail send attempted', [
            'id'       => $submission->id,
            'form_key' => $submission->form_key,
            'ok'       => $ok,
            'to'       => $to,
            'cc'       => $cc,
            'bcc'      => $bcc,
        ]);

        if (!$ok) {
            Log::error('Inbox notification: Brevo send failed', [
                'submission_id' => $submission->id,
                'form_key'      => $submission->form_key,
            ]);
        }
    }


    /**
     * Auto-replies for specific form types (visitor-facing emails).
     */
    protected function sendAutoReplies(FormSubmission $submission): void
    {
        $email = (string) ($submission->email ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $payload = $submission->payload ?? [];

        // ---- Investor Club auto-reply ----
        if ($submission->form_key === 'investor_club') {
            $name    = $submission->name ?: 'Investor';
            $subject = 'Welcome to the Sunshine Luxury Villas Investor Club';

            $html  = '<p>Dear ' . e($name) . ',</p>';
            $html .= '<p>Thank you for joining the <strong>Sunshine Luxury Villas Investor Club</strong>. ';
            $html .= 'We have received your details and a member of our team will be in touch shortly with more information ';
            $html .= 'about our latest opportunities.</p>';
            $html .= '<p>If you have any urgent questions, simply reply to this email.</p>';
            $html .= '<p>Best regards,<br>Sunshine Luxury Villas Team</p>';

            $ok = BrevoMail::send([$email], $subject, $html);

            if (!$ok) {
                Log::warning('Investor welcome mail failed via Brevo', [
                    'submission_id' => $submission->id,
                ]);
            }

            return;
        }

        // ---- Sell With Us auto-reply (formatted “card” layout) ----
        if ($submission->form_key === 'sell_with_us') {
            $ownerName = $submission->name ?: 'Property Owner';

            $pageUrl = $payload['page_url']
                ?? ($payload['url'] ?? 'https://www.sunshineluxuryvillas.co.uk/sell-with-us');

            $subject = 'We received your property submission — Sunshine Luxury Villas';

            $body  = '<div style="font-weight:800;font-size:18px;margin-bottom:6px;">Thank you for your submission</div>';
            $body .= '<div style="color:#6b7280;font-size:13px;margin-bottom:12px;">Reference: Sell With Us</div>';

            $body .= '<p style="margin:0 0 10px 0;">Dear ' . $this->e($ownerName) . ',</p>';

            $body .= '<p style="margin:0 0 10px 0;">
                Thank you for contacting <strong>Sunshine Luxury Villas</strong> about selling your property.
                We have received your details and a member of our team will review your information and contact you shortly
                (typically within <strong>24–48 hours</strong>).
            </p>';

            $body .= '<p style="margin:0 0 10px 0;">
                If you have any supporting information (photos, floor plans, title deed copies, renovations or valuation reports),
                you can reply to this email and attach them — it helps us evaluate and advise faster.
            </p>';

            $body .= $this->kvTable([
                'Submitted from' => '<a href="' . $this->e($pageUrl) . '" target="_blank">' . $this->e($pageUrl) . '</a>',
                'Your name'      => $this->e($submission->name ?? '—'),
                'Your email'     => $this->e($submission->email ?? '—'),
                'Your phone'     => $this->e($submission->phone ?? '—'),
            ]);

            $body .= '<p style="margin:14px 0 0 0;">
                Kind regards,<br><strong>Sunshine Luxury Villas</strong><br>
                <a href="mailto:enquires@sunshineluxuryvillas.com">enquires@sunshineluxuryvillas.com</a>
            </p>';

            $html = $this->mailWrap('Sell With Us Confirmation', $body);

            $ok = BrevoMail::send([$email], $subject, $html);

            if (!$ok) {
                Log::warning('Sell With Us auto-reply failed via Brevo', [
                    'submission_id' => $submission->id,
                ]);
            }
        }
    }

    // =========================
    // Helpers for email templates
    // =========================

    protected function mailWrap(string $title, string $bodyHtml): string
    {
        $css = '
            font-family: Arial, sans-serif;
            color: #111827;
            line-height: 1.5;
        ';

        return '
            <div style="' . $css . '">
                <div style="padding:16px 18px;border:1px solid #e5e7eb;border-radius:12px;max-width:680px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <div style="font-weight:700;font-size:14px;letter-spacing:.2px;">Sunshine Luxury Villas</div>
                        <div style="font-size:12px;color:#6b7280;">' . $this->e($title) . '</div>
                    </div>
                    <div style="border-top:1px solid #e5e7eb;margin:10px 0 14px;"></div>
                    ' . $bodyHtml . '
                </div>
                <div style="max-width:680px;font-size:12px;color:#9ca3af;margin-top:10px;">
                    This email was generated automatically from the SLV website forms.
                </div>
            </div>
        ';
    }

    protected function kvTable(array $rows): string
    {
        $out = '<table cellpadding="8" cellspacing="0" border="0" style="border-collapse:collapse;width:100%;margin-top:8px;">';

        foreach ($rows as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $out .= '
                <tr>
                    <td style="width:170px;font-weight:700;color:#374151;border-bottom:1px solid #f3f4f6;">' . $this->e($label) . '</td>
                    <td style="border-bottom:1px solid #f3f4f6;">' . $value . '</td>
                </tr>
            ';
        }

        $out .= '</table>';
        return $out;
    }

    protected function e(?string $value): string
    {
        return e($value ?? '');
    }
}
