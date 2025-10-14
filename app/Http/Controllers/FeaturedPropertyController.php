<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PropertiesModel;

class FeaturedPropertyController extends Controller
{
    public function save(Request $request)
    {
        $refs = collect($request->input('refs', []))
            ->map(fn ($r) => strtoupper(trim((string) $r)))
            ->filter()
            ->unique()
            ->take(12)
            ->values();

        // Nothing selected
        if ($refs->isEmpty()) {
            \App\Models\PropertiesModel::where('is_featured', 1)->update(['is_featured' => 0]);
            return response()->json(['ok' => true, 'count' => 0, 'missing' => []]);
        }

        // 1) Find matching properties by reference, CASE-INSENSITIVE
        $found = \App\Models\PropertiesModel::query()
            ->select('id', \DB::raw('UPPER(reference) as ref_upper'))
            ->whereIn(\DB::raw('UPPER(reference)'), $refs->all())
            ->get();

        $foundIds   = $found->pluck('id')->all();
        $foundUpper = $found->pluck('ref_upper')->all();

        $missing = $refs->reject(fn ($r) => in_array($r, $foundUpper, true))->values()->all();

        \DB::transaction(function () use ($foundIds) {
            // 2) Clear all old featured flags
            \App\Models\PropertiesModel::where('is_featured', 1)->update(['is_featured' => 0]);

            // 3) Set featured for the ones we actually found
            if (!empty($foundIds)) {
                \App\Models\PropertiesModel::whereIn('id', $foundIds)->update(['is_featured' => 1]);
            }
        });

        return response()->json([
            'ok'      => true,
            'count'   => count($foundIds),
            'missing' => $missing,
        ]);
    }
}
