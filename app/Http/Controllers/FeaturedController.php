<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertiesModel;
use Illuminate\Support\Facades\Cache;

class FeaturedController extends Controller
{
    // GET: Return all featured properties
    public function index()
    {
        $refs = Cache::get('featured_refs', []);
        if (empty($refs)) return response()->json([]);

        // pull ALL columns
        $properties = \App\Models\PropertiesModel::whereIn('reference', $refs)
            ->get()  // no select() -> returns all columns
            ->map(function ($p) {
                $p->location = implode(', ', array_filter([$p->town, $p->province, $p->country]));
                return $p;
            });

        return response()->json($properties);
    }


    // POST: Save featured property references
    public function store(Request $request)
    {
        $refs = $request->input('refs', []);

        if (!is_array($refs)) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        // Save to cache for 30 days
        Cache::put('featured_refs', $refs, now()->addDays(30));

        return response()->json([
            'success' => true,
            'count' => count($refs)
        ]);
    }
}
