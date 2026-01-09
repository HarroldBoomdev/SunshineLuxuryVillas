<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\ClientModel;      // ✅ use your real client model
use App\Models\PropertiesModel;  // ✅ your property model

class ClientRecommendationsController extends Controller
{
    /**
     * Return matching properties for the modal
     */
    public function list(Request $request, $clientId)
    {
        $client = ClientModel::findOrFail($clientId);

        // ✅ your client columns (exactly as in blade)
        $min = (float) ($client->MinimumPrice ?? 0);
        $max = (float) ($client->MaximumPrice ?? 0);

        // safety defaults
        if ($min < 0) $min = 0;
        if ($max <= 0) $max = 999999999;
        if ($min > $max) {
            // swap if wrong
            [$min, $max] = [$max, $min];
        }

        // ✅ match by property price
        $props = PropertiesModel::query()
            ->select([
                'id',
                'title',
                'reference',
                'price',
                'town',
                'region',
                'country',
                'photos',
            ])
            ->whereNotNull('price')
            ->where('price', '>=', $min)
            ->where('price', '<=', $max)
            ->orderBy('price', 'asc')
            ->limit(80)
            ->get()
            ->map(function ($p) {
                $photos = is_array($p->photos)
                    ? $p->photos
                    : (json_decode($p->photos ?? '[]', true) ?: []);

                $photo = $photos[0] ?? asset('images/no-image.jpg');

                return [
                    'id'        => $p->id,
                    'title'     => $p->title ?: 'Untitled',
                    'reference' => $p->reference ?: '',
                    'price'     => (float) ($p->price ?? 0),
                    'photo'     => $photo,
                    'location'  => trim(implode(', ', array_filter([$p->town, $p->region, $p->country]))),

                    // ✅ adjust this to your real public property page if different
                    'url'       => url("/properties/{$p->id}"),
                ];
            })
            ->values();

        return response()->json([
            // ✅ these keys must match your JS
            'client_name'  => trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
            'client_email' => (string) ($client->email ?? ''),
            'min_price'    => (int) $min,
            'max_price'    => (int) $max,
            'properties'   => $props,
        ]);
    }

    /**
     * Send email with selected properties
     */
    public function send(Request $request, $clientId)
    {
        $client = ClientModel::findOrFail($clientId);

        $request->validate([
            'property_ids'   => 'required|array|min:1',
            'property_ids.*' => 'integer',
        ]);

        if (!$client->email) {
            return response()->json(['ok' => false, 'message' => 'Client has no email.'], 422);
        }

        $ids = $request->property_ids;

        $properties = PropertiesModel::query()
            ->select([
                'id',
                'title',
                'reference',
                'price',
                'town',
                'region',
                'country',
                'photos',
                'property_description',
            ])
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($p) {
                $photos = is_array($p->photos)
                    ? $p->photos
                    : (json_decode($p->photos ?? '[]', true) ?: []);

                $photo = $photos[0] ?? asset('images/no-image.jpg');

                return [
                    'title_line' => $this->buildTitleLine($p),
                    'desc'       => (string) ($p->property_description ?? ''),
                    'reference'  => (string) ($p->reference ?? ''),
                    'price'      => (float) ($p->price ?? 0),
                    'photo'      => $photo,
                    'url'        => url("/properties/{$p->id}"),
                ];
            });

        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

        /** @var \App\Services\BrevoMailer $brevo */
        $brevo = app(\App\Services\BrevoMailer::class);

        // build a simple HTML email (works immediately, no Blade render needed)
        $html = view('emails.property_recommendations', [
            'clientName' => $clientName ?: 'Client',
            'properties' => $properties,
        ])->render();

        $text = strip_tags($html);

        // Send via Brevo API (NOT SMTP)
        $brevo->send(
            $client->email,
            'Property Recommendations',
            $html,
            $text
        );

        return response()->json(['ok' => true]);
    }

    private function buildTitleLine($p): string
    {
        // Keep it simple for now (you can enhance later with bedrooms/type)
        $loc = trim(implode(', ', array_filter([$p->town, $p->region])));
        $title = trim((string) ($p->title ?? 'Property'));
        return $loc ? "{$title}, {$loc}" : $title;
    }
}
