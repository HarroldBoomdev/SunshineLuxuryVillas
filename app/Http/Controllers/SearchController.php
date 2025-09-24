<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientModel;
use App\Models\PropertiesModel;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $type = $request->get('type');
        $term = $request->get('q', '');

        if ($type === 'client') {
            $clients = ClientModel::where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->limit(10)
                ->get();

            return response()->json(
                $clients->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'text' => trim($client->first_name . ' ' . $client->last_name)
                    ];
                })
            );
        }

        if ($type === 'property') {
            $properties = PropertiesModel::where('reference', 'like', "%{$term}%")
                ->orWhere('title', 'like', "%{$term}%")
                ->limit(10)
                ->get();

            return response()->json(
                $properties->map(function ($property) {
                    return [
                        'id' => $property->id,
                        'text' => "{$property->reference} – {$property->title}"
                    ];
                })
            );
        }

        return response()->json([]);
    }
}
