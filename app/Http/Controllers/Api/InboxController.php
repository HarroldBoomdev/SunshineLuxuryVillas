<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvestorWelcomeMail;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $q = FormSubmission::query()->latest();

        if ($request->filled('form_key')) {
            $q->where('form_key', $request->string('form_key'));
        }

        if ($request->filled('type')) {
            $type = $request->string('type');                 // e.g. investor-club
            $formKey = str_replace('-', '_', $type);          // investor_club

            // group the OR properly so it doesn't break other filters
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
        if (!isset($payload['message']) && $request->filled('message')) $payload['message'] = (string) $request->input('message');
        if (!isset($payload['message']) && !empty($data['enquiry']))   $payload['message'] = $data['enquiry'];
        if (!isset($payload['url']) && !empty($data['url']))           $payload['url']     = $data['url'];
        if (!isset($payload['page_url']) && $request->filled('page_url')) $payload['page_url'] = $request->string('page_url');
        if (!isset($payload['referrer']) && $request->filled('referrer')) $payload['referrer'] = $request->string('referrer');

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

        // 🔔 Auto-reply only for Investor Club sign-ups
        if (
            $submission->form_key === 'investor_club' &&
            filter_var($submission->email, FILTER_VALIDATE_EMAIL)
        ) {
            try {
                Mail::mailer('investorclub')
                    ->to($submission->email)
                    ->send(new InvestorWelcomeMail($submission->name ?? ''));
            } catch (\Throwable $e) {
                Log::warning('Investor welcome mail failed', [
                    'submission_id' => $submission->id,
                    'error' => $e->getMessage(),
                ]);
                // don't break API response
            }
        }

        return response()->json(['ok' => true, 'id' => $submission->id], 201);
    }
}
