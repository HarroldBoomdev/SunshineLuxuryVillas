<?php

namespace App\Http\Controllers;

use App\Models\ClientModel;
use App\Models\PropertiesModel;
use App\Services\BrevoMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientRecommendationsController extends Controller
{
    /**
     * Return matching properties for the modal
     */
    public function list(Request $request, $clientId)
    {
        $client = ClientModel::findOrFail($clientId);

        // use your actual columns (lowercase are the real ones you showed in tinker)
        $min = (float) ($client->MinimumPrice ?? 0);
        $max = (float) ($client->MaximumPrice ?? 0);

        if ($min < 0) $min = 0;
        if ($max <= 0) $max = 999999999;
        if ($min > $max) [$min, $max] = [$max, $min];

        $props = PropertiesModel::query()
            ->select(['id','title','reference','price','town','region','country','photos'])
            ->whereNotNull('price')
            ->whereBetween('price', [$min, $max])
            ->orderBy('price', 'asc')
            ->limit(80)
            ->get()
            ->map(function ($p) {
                $photos = is_array($p->photos)
                    ? $p->photos
                    : (json_decode($p->photos ?? '[]', true) ?: []);

                return [
                    'id'        => (int) $p->id,
                    'title'     => $p->title ?: 'Untitled',
                    'reference' => $p->reference ?: '',
                    'price'     => (float) ($p->price ?? 0),
                    'photo'     => $photos[0] ?? null,
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
     * Send email with selected properties
     */
    public function send(Request $request, $clientId)
    {
        $client = ClientModel::findOrFail($clientId);

        $data = $request->validate([
            'property_ids'   => ['required','array','min:1'],
            'property_ids.*' => ['integer'],
        ]);

        if (empty($client->email)) {
            return response()->json(['ok' => false, 'message' => 'Client has no email.'], 422);
        }

        // IDs received from frontend
        $ids = array_values(array_unique(array_map('intval', $data['property_ids'])));

        // Fetch found properties
        $found = PropertiesModel::query()
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
            ->keyBy('id');

        // Keep the same order as user selection + drop missing ids safely
        $ordered = collect($ids)
            ->map(fn ($id) => $found->get($id))
            ->filter()
            ->values();

        // LOG the real reason when counts mismatch
        Log::info('Recommendations send()', [
            'client_id' => $clientId,
            'email' => $client->email,
            'ids_received_count' => count($ids),
            'ids_received' => $ids,
            'found_count' => $ordered->count(),
            'missing_ids' => array_values(array_diff($ids, $ordered->pluck('id')->map(fn($v)=>(int)$v)->all())),
        ]);

        if ($ordered->isEmpty()) {
            return response()->json([
                'ok' => false,
                'message' => 'No properties found for selected IDs.'
            ], 422);
        }

        $properties = $ordered->map(function ($p) {
            $photos = is_array($p->photos)
                ? $p->photos
                : (json_decode($p->photos ?? '[]', true) ?: []);

            $photo = $photos[0] ?? null;

            // IMPORTANT: strip risky html so email doesn't break mid-loop
            $desc = (string) ($p->property_description ?? '');
            $desc = trim(strip_tags($desc));
            if (mb_strlen($desc) > 700) {
                $desc = mb_substr($desc, 0, 700) . '…';
            }

            return [
                'id'         => (int) $p->id,
                'title_line' => $this->buildTitleLine($p),
                'desc'       => $desc,
                'reference'  => (string) ($p->reference ?? ''),
                'price'      => (float) ($p->price ?? 0),
                'photo'      => $photo,
                'url'        => url("/properties/{$p->id}"),
            ];
        })->all(); // convert to plain array for Blade stability

        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
        $clientName = $clientName ?: 'Client';

        $html = view('emails.property_recommendations', [
            'clientName' => $clientName,
            'properties' => $properties,
        ])->render();

        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));

        /** @var BrevoMailer $brevo */
        $brevo = app(BrevoMailer::class);

        $brevo->send(
            $client->email,
            'Property Recommendations',
            $html,
            $text
        );

        return response()->json([
            'ok' => true,
            'sent' => count($properties),
        ]);
    }

    private function buildTitleLine($p): string
    {
        $loc = trim(implode(', ', array_filter([$p->town ?? null, $p->region ?? null, $p->country ?? null])));
        $title = trim((string) ($p->title ?? 'Property'));
        return $loc ? "{$title}, {$loc}" : $title;
    }
}
