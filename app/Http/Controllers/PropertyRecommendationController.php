<?php

namespace App\Http\Controllers;

use App\Models\ClientModel;
use Illuminate\Support\Facades\Mail;
use App\Mail\PropertyRecommendationsMail;
use Illuminate\Http\Request;
use App\Models\PropertiesModel;

class PropertyRecommendationController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $limit = (int) $request->query('limit', 15);
        $limit = max(1, min($limit, 50));

        $query = PropertiesModel::query()
            ->select(['id','reference','title','location','property_type','bedrooms','price','photos']);

        // ✅ suggested list when empty / < 2 chars
        if ($q === '' || mb_strlen($q) < 2) {
            $items = $query->inRandomOrder()
                ->limit($limit)
                ->get()
                ->map(fn($p) => $this->mapProperty($p))
                ->values();

            return response()->json([
                'ok' => true,
                'mode' => 'suggested',
                'items' => $items,
            ]);
        }

        $term = '%' . addcslashes($q, '%_') . '%';

        $items = $query->where(function ($qq) use ($term) {
                $qq->where('reference', 'like', $term)
                   ->orWhere('title', 'like', $term)
                   ->orWhere('property_type', 'like', $term)
                   ->orWhere('location', 'like', $term);
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn($p) => $this->mapProperty($p))
            ->values();

        return response()->json([
            'ok' => true,
            'mode' => 'search',
            'items' => $items,
        ]);
    }

    protected function mapProperty($p): array
    {
        return [
            'id'            => $p->id,
            'reference'     => (string)($p->reference ?? ''),
            'title'         => (string)($p->title ?? 'Untitled'),
            'location'      => (string)($p->location ?? 'N/A'),
            'property_type' => (string)($p->property_type ?? ''),
            'bedrooms'      => $p->bedrooms,
            'price'         => $p->price,
            'thumb'         => $this->firstPhotoUrl($p->photos),
        ];
    }

    protected function firstPhotoUrl($raw): ?string
    {
        $arr = $raw;

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) $arr = $decoded;
            else return preg_match('#^https?://#i', $raw) ? $raw : null;
        }

        if (!is_array($arr)) return null;

        foreach ($arr as $item) {
            if (is_string($item) && preg_match('#^https?://#i', $item)) return $item;

            if (is_array($item)) {
                foreach (['url','src','path','thumbnail','original','image','href'] as $k) {
                    if (!empty($item[$k]) && is_string($item[$k]) && preg_match('#^https?://#i', $item[$k])) {
                        return $item[$k];
                    }
                }
            }
        }

        return null;
    }

    public function searchClients(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $limit = (int) $request->query('limit', 10);
        $limit = max(1, min($limit, 50));

        if (mb_strlen($q) < 3) {
            return response()->json(['ok' => true, 'items' => []]);
        }

        $term = '%' . str_replace(['%','_'], ['\%','\_'], $q) . '%';

        $items = ClientModel::query()
            ->select(['id','first_name','last_name','email'])
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where(function($qq) use ($term){
                $qq->where('email', 'like', $term)
                ->orWhere('first_name', 'like', $term)
                ->orWhere('last_name', 'like', $term)
                ->orWhereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", [$term]);
            })
            ->orderBy('last_name')
            ->limit($limit)
            ->get()
            ->map(function($c){
                return [
                    'id'    => $c->id,
                    'name'  => trim(($c->first_name ?? '').' '.($c->last_name ?? '')),
                    'email' => $c->email,
                ];
            });

        return response()->json(['ok' => true, 'items' => $items]);
    }

    public function send(Request $request)
    {
        $propertyIds = collect($request->input('property_ids', []))
            ->map(fn($v) => (int)$v)->filter()->unique()->values();

        $clientIds = collect($request->input('client_ids', []))
            ->map(fn($v) => (int)$v)->filter()->unique()->values();

        if ($propertyIds->isEmpty() || $clientIds->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'Select at least 1 property and 1 client.'], 422);
        }

        $properties = Property::query()
            ->select(['id','reference','title','location','property_type','bedrooms','price','photos'])
            ->whereIn('id', $propertyIds->all())
            ->get()
            ->map(fn($p) => $this->mapProperty($p)); // uses your existing mapProperty()

        $clients = ClientModel::query()
            ->select(['id','first_name','last_name','email'])
            ->whereIn('id', $clientIds->all())
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($clients as $c) {
            $email = trim((string)$c->email);
            if ($email === '') { $skipped++; continue; }

            $name = trim(($c->first_name ?? '').' '.($c->last_name ?? ''));

            Mail::to($email)->send(new PropertyRecommendationsMail($name ?: 'Client', $properties->all()));
            $sent++;
        }

        return response()->json([
            'ok' => true,
            'sent' => $sent,
            'skipped' => $skipped,
        ]);
    }
}
