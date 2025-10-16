<?php

namespace App\Jobs;

use App\Models\PropertiesModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPropertyToFrontend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $propertyId) {}

    public function handle(): void
    {
        $p = PropertiesModel::find($this->propertyId);
        if (!$p) return;

        $base = rtrim(config('services.frontend_api.base'), '/');
        $key  = config('services.frontend_api.key');

        if (!$base || !$key) {
            Log::warning('Frontend API not configured; skipping sync', ['id' => $this->propertyId]);
            return;
        }

        if ($p->property_status === 'Active') {
            // PUBLISH / UPSERT
            $payload = [
                'reference'   => $p->reference,
                'title'       => $p->title,
                'type'        => $p->property_type,
                'region'      => $p->region,
                'town'        => $p->town,
                'address'     => $p->address,
                'price'       => $p->price,
                'bedrooms'    => $p->bedrooms,
                'bathrooms'   => $p->bathrooms,
                'plot_m2'     => $p->plot_m2,
                'covered_m2'  => $p->covered_m2 ?? $p->internal_area,
                'labels'      => $p->labels,
                'photos'      => $p->photos ?? [],
                'description' => $p->property_description,
                'is_live'     => true,
            ];

            $resp = Http::withToken($key)->post($base . '/properties/upsert', $payload);

            if ($resp->successful()) {
                $body = $resp->json() ?? [];
                $p->forceFill([
                    'external_slug' => $body['slug'] ?? $p->external_slug,
                    'published_at'  => now(),
                ])->saveQuietly();
            } else {
                Log::warning('Frontend publish failed', [
                    'id' => $p->id, 'status' => $resp->status(), 'body' => $resp->body()
                ]);
                $this->release(60); // retry in 60s
            }

        } else {
            // UNPUBLISH
            if ($p->external_slug) {
                $resp = Http::withToken($key)->delete($base . '/properties/' . $p->external_slug);
                if ($resp->failed()) {
                    Log::warning('Frontend unpublish failed', [
                        'id' => $p->id, 'status' => $resp->status(), 'body' => $resp->body()
                    ]);
                }
            }
            $p->forceFill(['published_at' => null])->saveQuietly();
        }
    }
}
