<?php

namespace App\Http\Controllers;

use App\Mail\FormSubmissionReceived;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class InboxController extends Controller
{
    /**
     * Left nav tabs for /inbox (web UI)
     * Keys are the "type" slugs (hyphenated) used by the UI filter.
     */
    private array $tabs = [
        'property-details' => 'Property Details',
        'investor-club'    => 'Investor Club',
        'sell-with-us'     => 'Sell With Us',
        'contact-us'       => 'Contact Us',
        'affiliate-page'   => 'Affiliate Page',
        'request-callback' => 'Request a Callback',
        'subscribe'        => 'Subscribe',
    ];

    /**
     * GET /inbox (protected by web middleware)
     * Robust filter: matches by `type` (hyphen) OR `form_key` (underscore).
     */
    public function index(Request $request)
    {
        $defaultType = array_key_first($this->tabs);

        $data = $request->validate([
            'type' => ['nullable', Rule::in(array_keys($this->tabs))],
            's'    => ['nullable', 'string', 'max:200'],
        ]);

        $type   = $data['type'] ?? $defaultType;          // e.g. "request-callback"
        $search = $data['s']    ?? null;

        // Convert "request-callback" -> "request_callback" to match DB form_key
        $formKeyFromType = str_replace('-', '_', $type);

        $query = FormSubmission::query()
            ->where(function ($q) use ($type, $formKeyFromType) {
                $q->where('type', $type)
                  ->orWhere('form_key', $formKeyFromType);
            });

        if ($search) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('phone', 'like', $like)
                  ->orWhere('reference', 'like', $like);
            });
        }

        $submissions = $query->latest()->paginate(20)->withQueryString();

        return view('inbox.index', [
            'tabs'        => $this->tabs,
            'type'        => $type,
            'submissions' => $submissions,
            'search'      => $search,
        ]);
    }

    /**
     * GET /inbox/{id}
     */
    public function show(int $submissionId)
    {
        $submission = FormSubmission::findOrFail($submissionId);

        return view('inbox.show', [
            'submission' => $submission,
            'tabs'       => $this->tabs,
            'type'       => $submission->type,
        ]);
    }

    /**
     * DELETE /inbox/{id}
     */
    public function destroy(int $id)
    {
        $submission   = FormSubmission::findOrFail($id);
        $redirectType = $submission->type; // keep user on the same tab
        $submission->delete();

        return redirect()
            ->route('inbox.index', ['type' => $redirectType])
            ->with('success', 'Submission deleted successfully.');
    }

    /**
     * (Web UI) Manual outbound send
     * POST /inbox/{id}/send  (protected)
     */
    public function send(int $id, Request $request)
    {
        $submission = FormSubmission::findOrFail($id);

        $data = $request->validate([
            'action'  => 'required|in:reply,reply_all,forward',
            'to'      => 'required|string', // comma-separated
            'cc'      => 'nullable|string',
            'bcc'     => 'nullable|string',
            'subject' => 'required|string|max:200',
            'body'    => 'required|string',
        ]);

        $replyTo     = config('mail.reply_to.address') ?? 'inbox@sunshineluxuryvillas.co.uk';
        $replyToName = config('mail.reply_to.name')    ?? 'SLV Inbox';

        $to  = array_map('trim', explode(',', $data['to']));
        $cc  = $data['cc']  ? array_map('trim', explode(',', $data['cc']))  : [];
        $bcc = $data['bcc'] ? array_map('trim', explode(',', $data['bcc'])) : [];

        Mail::send('mail.outbound', ['htmlBody' => nl2br(e($data['body']))], function ($m) use ($to, $cc, $bcc, $data, $replyTo, $replyToName) {
            $m->subject($data['subject']);
            $m->to($to);
            if (!empty($cc))  $m->cc($cc);
            if (!empty($bcc)) $m->bcc($bcc);
            if ($replyTo)     $m->replyTo($replyTo, $replyToName);
        });

        // Optional: persist outbound message
        if (Schema::hasTable('outbound_messages')) {
            \DB::table('outbound_messages')->insert([
                'form_submission_id' => $submission->id,
                'action'     => $data['action'],
                'to'         => $data['to'],
                'cc'         => $data['cc']  ?: null,
                'bcc'        => $data['bcc'] ?: null,
                'subject'    => $data['subject'],
                'body'       => $data['body'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Email sent successfully.');
    }

    /**
     * API-style save entrypoint
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'form_key'  => ['required', 'string', 'max:100'],
            'name'      => ['nullable', 'string', 'max:150'],
            'email'     => ['nullable', 'email', 'max:150'],
            'phone'     => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:100'],
            'payload'   => ['nullable', 'array'],
        ]);

        $type = $this->mapFormKeyToType($data['form_key']);

        $submission = FormSubmission::create([
            'form_key'  => $data['form_key'],
            'type'      => $type,
            'name'      => $data['name']      ?? null,
            'email'     => $data['email']     ?? null,
            'phone'     => $data['phone']     ?? null,
            'reference' => $data['reference'] ?? null,
            'payload'   => $data['payload']   ?? null,
        ]);

        // --- Build routing from config (config/form_inbox.php) ---
        $map   = config('form_inbox', []);
        $route = $map[$data['form_key']] ?? ($map['_default'] ?? []);

        $to  = array_values(array_filter($route['to']  ?? []));
        $cc  = array_values(array_filter(array_merge($map['_always'] ?? [], $route['cc'] ?? [])));
        $bcc = array_values(array_filter($route['bcc'] ?? []));

        // Ensure we always have at least one primary recipient
        if (empty($to) && !empty($map['_default']['to'])) {
            $to = (array) $map['_default']['to'];
        }

        $mailError = null;

        // --- INTERNAL NOTIFICATION ---
        try {
            Mail::to($to)
                ->cc($cc)
                ->bcc($bcc)
                ->send(new FormSubmissionReceived($submission));
        } catch (\Throwable $e) {
            $mailError = $e->getMessage();
            Log::error('Form submission mail failed', [
                'submission_id' => $submission->id,
                'error'         => $mailError,
                'to'            => $to,
                'cc'            => $cc,
                'bcc'           => $bcc,
            ]);
        }

        // --- AUTO-REPLY TO LEAD (if they provided an email) ---
        if (!empty($data['email'])) {
            try {
                Mail::send('emails.auto_reply', ['data' => $data, 'submission' => $submission], function ($m) use ($data) {
                    $m->to($data['email'])
                      ->subject('Thanks for contacting Sunshine Luxury Villas');
                });
            } catch (\Throwable $e) {
                Log::warning('Auto-reply failed', [
                    'submission_id' => $submission->id,
                    'error'         => $e->getMessage(),
                    'lead_email'    => $data['email'],
                ]);
            }
        }

        return response()->json([
            'ok'       => true,
            'id'       => $submission->id,
            'sent_to'  => ['to' => $to, 'cc' => $cc, 'bcc' => $bcc],
            'mail_err' => $mailError,
        ]);
    }

    /**
     * Map API form keys (underscored) to tab "type" slugs (hyphenated).
     */
    private function mapFormKeyToType(string $formKey): string
    {
        return match ($formKey) {
            'property_details' => 'property-details',
            'investor_club'    => 'investor-club',
            'sell_with_us'     => 'sell-with-us',
            'contact_us'       => 'contact-us',
            'affiliate'        => 'affiliate-page',
            'request_callback' => 'request-callback',
            'subscribe'        => 'subscribe',
            default            => str_replace('_', '-', $formKey),
        };
    }

    public function requestCallback(Request $request)
    {
        $submissions = FormSubmission::where('form_key', 'request_callback')
            ->latest()
            ->paginate(20);

        return view('inbox.index', [
            'tabs'        => $this->tabs ?? [],
            'type'        => 'request_callback',
            'submissions' => $submissions,
            'search'      => null,
        ]);
    }
}
