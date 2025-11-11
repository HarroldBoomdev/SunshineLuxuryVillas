<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\PropertiesModel as Property;

class FeedController extends Controller
{
    public function properties(Request $request)
    {
        $since    = $request->query('since');
        $region   = $request->query('region');
        $town     = $request->query('town');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $limit    = min((int)($request->query('limit', 5000)), 20000);

        $cacheKey = 'feed:properties:' . sha1(json_encode([$since,$region,$town,$minPrice,$maxPrice,$limit]));

        $payload = Cache::remember($cacheKey, 300, function () use ($since,$region,$town,$minPrice,$maxPrice,$limit) {
            $q = Property::query()
                ->whereNull('deleted_at')
                ->when($since,    fn($qq) => $qq->where('updated_at', '>=', Carbon::parse($since)))
                ->when($region,   fn($qq) => $qq->where('region', $region))
                ->when($town,     fn($qq) => $qq->where('town', $town))
                ->when($minPrice, fn($qq) => $qq->where('price', '>=', (int)$minPrice))
                ->when($maxPrice, fn($qq) => $qq->where('price', '<=', (int)$maxPrice))
                ->orderByDesc('updated_at');

            $props   = $q->limit($limit)->get();
            $lastMod = optional($props->max('updated_at'))?->toRfc7231String() ?? now()->toRfc7231String();
            $etag    = sha1(($props->count() ?: 0) . '|' . $lastMod);

            return compact('props','lastMod','etag');
        });

        if ($request->header('If-None-Match') === $payload['etag']) {
            return response('', 304)
                ->header('ETag', $payload['etag'])
                ->header('Last-Modified', $payload['lastMod']);
        }

        $xml = view('feeds.properties', [
            'properties'  => $payload['props'],
            'generatedAt' => now()->toIso8601String(),
        ])->render();

        // ✅ prepend the XML declaration safely
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . $xml;

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('ETag', $payload['etag'])
            ->header('Last-Modified', $payload['lastMod']);
    }
}
