<?php

namespace App\Http\Controllers;

use App\Models\FeaturedProperty;
use App\Models\PropertiesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeaturedPropertyController extends Controller
{
    // Save the featured properties (replace current list)
    public function save(Request $request)
    {
        $data = $request->validate([
            'refs'   => ['required', 'array', 'max:12'],
            'refs.*' => ['string', 'distinct', Rule::exists((new PropertiesModel)->getTable(), 'reference')],
        ]);

        $refs = array_values($data['refs']); // keep order

        DB::transaction(function () use ($refs) {
            FeaturedProperty::query()->delete(); // clear old list
            foreach ($refs as $i => $ref) {
                FeaturedProperty::create([
                    'reference' => $ref,
                    'position'  => $i,
                ]);
            }
        });

        return response()->json([
            'ok'    => true,
            'count' => count($refs),
        ]);
    }
}
