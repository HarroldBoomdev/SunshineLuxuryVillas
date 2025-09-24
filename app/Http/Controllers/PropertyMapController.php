<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertiesModel;

class PropertyMapController extends Controller
{
    public function index()
    {
        return PropertiesModel::select('id', 'title', 'price', 'location', 'photos')
            ->get()
            ->map(function ($property) {
                // Decode JSON photos and use first image if available
                $photos = json_decode($property->photos, true);
                $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : null;

                return [
                    'id'        => $property->id,
                    'title'     => $property->title,
                    'price'     => $property->price,
                    'location'  => $property->location,
                    'photo'     => $firstPhoto ?: 'https://placehold.co/400x250?text=' . urlencode($property->location),
                    // 🚧 Fallback demo coordinates for now, to test UI
                    'latitude'  => fakeLatitude($property->location),
                    'longitude' => fakeLongitude($property->location),
                ];
            });
    }
}

function fakeLatitude($location)
{
    return match (strtolower($location)) {
        'nicosia'  => 35.1856,
        'larnaca'  => 34.9206,
        'paphos'   => 34.7720,
        'limassol' => 34.7071,
        'peyia'    => 34.8783,
        'armou'    => 34.7650,
        'livadia'  => 34.9466,
        'drosia'   => 34.9225,
        default    => 34.9, // center of Cyprus
    };
}

function fakeLongitude($location)
{
    return match (strtolower($location)) {
        'nicosia'  => 33.3823,
        'larnaca'  => 33.6232,
        'paphos'   => 32.4297,
        'limassol' => 33.0226,
        'peyia'    => 32.3701,
        'armou'    => 32.4700,
        'livadia'  => 33.6485,
        'drosia'   => 33.6241,
        default    => 33.2, // center of Cyprus
    };
}
