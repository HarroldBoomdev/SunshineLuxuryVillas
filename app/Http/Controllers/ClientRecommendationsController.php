<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientModel;
use App\Models\PropertiesModel;

class ClientRecommendationsController extends Controller
{
    /**
     * Return matching properties for the modal
     */
    public function list(Request $request, $clientId)
    {
        $client = ClientModel::findOrFail($clientId);

        // Client budget fields (your DB columns)
        $min = (float) ($client->MinimumPrice ?? 0);
        $max = (float) ($client->MaximumPrice ?? 0);

        // Safety defaults
        if ($min < 0) $min = 0;
        if ($max <= 0) $max = 999999999;
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

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
                    'id'        => (int) $p->id,
                    'title'     => $p->title ?: 'Untitled',
                    'reference' => $p->reference ?: '',
                    'price'     => (float) ($p->price ?? 0),
                    'photo'     => $photo,
                    'location'  => trim(implode(', ', array_filter([$p->town, $p->region, $p->country]))),
                    'url'       => url("/properties/{$p->id}"),
                ];
            })
            ->values();

        return response()->json([
            'client_name'  => trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
            'client_email' => (string) ($client->email ?? ''),
            'min_price'    => (int) $min,
            'max_price'    => (int) $max,
            'properties'   => $props,
        ]);
    }

    /**
     * Send email with selected properties (Brevo API, NOT SMTP)
     */
    public function send(Request $request, $clientId)
    {
        $client = ClientModel::findOrFail($clientId);

        $request->validate([
            'property_ids'   => 'required|array|min:1',
            'property_ids.*' => 'integer',
        ]);

        if (empty($client->email)) {
            return response()->json(['ok' => false, 'message' => 'Client has no email.'], 422);
        }

        // ✅ Clean + de-dup ids
        $ids = array_values(array_unique(array_map('intval', $request->property_ids)));

        // ✅ Hard safety limit to avoid Brevo payload issues
        $maxItems = 5;
        $ids = array_slice($ids, 0, $maxItems);

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
            // ✅ Preserve user-selected order
            ->orderByRaw('FIELD(id,' . implode(',', $ids) . ')')
            ->get()
            ->map(function ($p) {
                $photos = is_array($p->photos)
                    ? $p->photos
                    : (json_decode($p->photos ?? '[]', true) ?: []);

                $photo = $photos[0] ?? asset('images/no-image.jpg');

                // ✅ Trim description to keep email small
                $desc = (string) ($p->property_description ?? '');
                $desc = trim(strip_tags($desc));
                if (mb_strlen($desc) > 500) {
                    $desc = mb_substr($desc, 0, 500) . '...';
                }

                return [
                    'title_line' => $this->buildTitleLine($p),
                    'desc'       => $desc,
                    'reference'  => (string) ($p->reference ?? ''),
                    'price'      => (float) ($p->price ?? 0),
                    'photo'      => $photo,
                    'url'        => url("/properties/{$p->id}"),
                ];
            });

        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: 'Client';

        // Build HTML from Blade
        $html = view('emails.property_recommendations', [
            'clientName' => $clientName,
            'properties' => $properties,
        ])->render();

        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));

        /** @var \App\Services\BrevoMailer $brevo */
        $brevo = app(\App\Services\BrevoMailer::class);

        // ✅ Brevo API send (expects 4 args)
        $brevo->send(
            (string) $client->email,
            'Property Recommendations',
            $html,
            $text
        );

        return response()->json(['ok' => true]);
    }

    private function buildTitleLine($p): string
    {
        $loc = trim(implode(', ', array_filter([$p->town, $p->region])));
        $title = trim((string) ($p->title ?? 'Property'));
        return $loc ? "{$title}, {$loc}" : $title;
    }
}
