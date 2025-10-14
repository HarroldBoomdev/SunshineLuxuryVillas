<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\PropertiesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class PropertiesController extends Controller
{
    public function apiIndex(Request $request)
    {
        $perPage = $request->input('per_page', 12);

        $query = PropertiesModel::query()->select([
            'id',
            'title',
            'price',
            'bedrooms',
            'bathrooms',
            'property_type',
            'location',
            'province',
            'town',
            'built_area',
            'plot_area',
            'reference',
            'url',
            'property_description',
            'photos',
        ]);

        $region = $request->query('region') ?: $request->query('province');
        if (!empty($region)) {
            $needle = mb_strtolower(trim($region));
            $query->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(province) = ?', [$needle])
                ->orWhereRaw('LOWER(town) = ?', [$needle]);
            });
        }


        $properties = $query->paginate($perPage);

        return PropertyResource::collection($properties);
    }

    public function top()
    {
        $properties = PropertiesModel::where('is_featured', true)
            ->latest()
            ->take(12)
            ->get(['id', 'title', 'price', 'photos']);

        $properties->transform(function ($property) {
            $photos = is_string($property->photos)
                ? json_decode(str_replace("'", '"', $property->photos), true)
                : $property->photos;

            $property->photo = $property->photo ?: ($photos[0] ?? null);
            unset($property->photos);
            return $property;
        });

        return response()->json($properties);
    }

    public function featured()
    {
        // Prefer the curated admin list (table created/used by admin.featured.save)
        if (Schema::hasTable('featured_properties')) {

            // If your table stores PROPERTY IDs:
            if (Schema::hasColumn('featured_properties', 'property_id')) {
                $ids = collect(
                    DB::table('featured_properties')
                        ->orderBy('position')
                        ->pluck('property_id')
                        ->all()
                )->filter()->values();

                if ($ids->isNotEmpty()) {
                    $map = PropertiesModel::whereIn('id', $ids)->get()->keyBy('id');
                    $ordered = $ids->map(fn ($id) => $map->get($id))->filter()->values()->take(12);
                    return response()->json($ordered, 200);
                }
            }

            // If your table stores REFERENCES:
            if (Schema::hasColumn('featured_properties', 'reference')) {
                $refs = collect(
                    DB::table('featured_properties')
                        ->orderBy('position')
                        ->pluck('reference')
                        ->all()
                )->map(fn ($r) => strtoupper(trim($r)))
                ->filter()->values();

                if ($refs->isNotEmpty()) {
                    $map = PropertiesModel::whereIn('reference', $refs)->get()
                        ->keyBy(fn ($p) => strtoupper($p->reference));
                    $ordered = $refs->map(fn ($r) => $map->get($r))->filter()->values()->take(12);
                    return response()->json($ordered, 200);
                }
            }
        }

        // Fallback (only if curated list empty/missing)
        $items = PropertiesModel::where('is_featured', 1)
            ->latest('updated_at')
            ->limit(12)
            ->get();

        return response()->json($items, 200);
    }



    public function recent()
    {
        $recent = PropertiesModel::latest()
            ->take(5)
            ->get();

        return response()->json($recent);
    }

    public function filters()
    {
        return response()->json([
            'types'    => PropertiesModel::select('property_type')->distinct()->pluck('property_type')->filter()->values(),
            'regions'  => PropertiesModel::select('province')->distinct()->pluck('province')->filter()->values(),
            'bedrooms' => PropertiesModel::select('bedrooms')->distinct()->pluck('bedrooms')->filter()->sort()->values(),
        ]);
    }

    public function show($reference)
    {
        $property = \App\Models\PropertiesModel::query()
            ->where('id', $reference)
            ->orWhere('reference', $reference)
            ->firstOrFail();

        return new \App\Http\Resources\PropertyResource($property);
    }

    public function showByReference(string $ref)
    {
        $ref = strtoupper(trim($ref));

        $p = PropertiesModel::query()
            ->whereRaw('UPPER(reference) = ?', [$ref])
            ->first();

        if (!$p) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($p);
    }

    /**
     * POST /api/properties/by-ref { reference: "TGAALA" }
     * Same as GET but via POST body.
     */
    public function findByReference(Request $request)
    {
        $ref = strtoupper(trim($request->input('reference', '')));
        if ($ref === '') {
            return response()->json(['message' => 'reference required'], 422);
        }

        $p = PropertiesModel::query()
            ->whereRaw('UPPER(reference) = ?', [$ref])
            ->first();

        if (!$p) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($p);
    }

}
