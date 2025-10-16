<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FrontendSyncService
{
    protected string $base;
    protected ?string $key;

    public function __construct()
    {
        $this->base = rtrim(config('services.frontend_api.base', ''), '/');
        $this->key  = config('services.frontend_api.key');
    }

    protected function client()
    {
        $headers = [];
        if ($this->key) {
            $headers['Authorization'] = 'Bearer ' . $this->key;
        }

        return Http::timeout((int)config('services.frontend_api.timeout', 20))
            ->withHeaders($headers)
            ->acceptJson()
            ->baseUrl($this->base);
    }

    public function upsert(array $payload): array
    {
        $res = $this->client()->post('/properties/upsert', $payload);

        Log::info('Frontend upsert response', [
            'status' => $res->status(),
            'body'   => $res->json() ?? $res->body(),
        ]);

        return ['ok' => $res->successful(), 'response' => $res->json()];
    }

    public function unpublish(string $slug): array
    {
        $res = $this->client()->delete("/properties/{$slug}");

        Log::info('Frontend unpublish response', [
            'status' => $res->status(),
            'body'   => $res->json() ?? $res->body(),
        ]);

        return ['ok' => $res->successful(), 'response' => $res->json()];
    }
}
