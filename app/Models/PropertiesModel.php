<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertiesModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'properties';

    protected $fillable = [
        'title',
        'property_description',
        'reference',
        'property_type',
        'floors',
        'parkingSpaces',
        'bathrooms',
        'bedrooms',
        'furnished',
        'orientation',
        'year_construction',
        'year_renovation',
        'price',
        'vat',
        'status',
        'basement',
        'energyEfficiency',
        'covered',
        'attic',
        'coveredVeranda',
        'coveredParking',
        'courtyard',
        'roofGarden',
        'uncoveredVeranda',
        'plot',
        'garden',
        'owner',
        'refId',
        'region',
        'town',
        'address',
        'labels',
        'image_order',
        'photos',
        'photo', // keep if the table has a dedicated photo column

        // ---- NEW: Land fields (match DB) ----
        'regnum',       // registration number
        'plotnum',
        'section',
        'sheetPlan',
        'titleDead',    // available|in_process|no_title|-
        'share',

        // ---- NEW: Distances (km) ----
        'amenities',
        'airport',
        'sea',
        'publicTransport',
        'schools',
        'resort',

        // (optional extras you likely have)
        'facilities',
        'titledeed',    // JSON array of title deed image paths (if used)
    ];

    protected $casts = [
        // arrays / json
        // 'labels' => 'array', // uncomment if labels stored as JSON
        'facilities'   => 'array',
        'titledeed'    => 'array',

        // numbers
        'price'            => 'float',
        'vat'              => 'float',
        'covered'          => 'float',
        'coveredVeranda'   => 'float',
        'coveredParking'   => 'float',
        'uncoveredVeranda' => 'float',
        'plot'             => 'float',
        'garden'           => 'float',
        'share'            => 'float',

        // distances
        'amenities'       => 'float',
        'airport'         => 'float',
        'sea'             => 'float',
        'publicTransport' => 'float',
        'schools'         => 'float',
        'resort'          => 'float',

        // dates
        'movedinReady' => 'date',
    ];

    /**
     * Accessor: normalize photos column to always return array of URLs.
     */
    public function getPhotosAttribute($value)
    {
        if (empty($value)) return [];

        if (is_array($value)) return $value;

        if (is_string($value)) {
            $normalized = str_replace("'", '"', $value);
            $decoded = json_decode($normalized, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return array_filter(array_map('trim', explode(',', $value)));
        }

        return [];
    }

    /**
     * Accessor: cover photo (DB column 'photo' if set, otherwise first from photos).
     */
    public function getPhotoAttribute($value)
    {
        if (is_string($value) && $value !== '') return $value;

        $photos = $this->getPhotosAttribute($this->attributes['photos'] ?? null);

        if (is_array($photos) && isset($photos[0])) {
            if (is_string($photos[0])) return $photos[0];

            if (is_array($photos[0])) {
                return $photos[0]['url']
                    ?? $photos[0]['src']
                    ?? $photos[0]['path']
                    ?? $photos[0]['image']
                    ?? $photos[0]['photo_url']
                    ?? null;
            }
        }

        return null;
    }

    public function getLabelsAttribute($value)
    {
        if (is_array($value)) return $value;
        if ($value === null || $value === '') return [];

        $trimmed = trim($value);

        if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
            $decoded = json_decode($trimmed, true);
            return is_array($decoded) ? array_map('trim', $decoded) : [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    public function getLivingRoomsAttribute($value)
    {
        return $this->attributes['livingRooms'] ?? $value;
    }

    public function getParkingSpacesAttribute($value)
    {
        return $this->attributes['parkingSpaces'] ?? $value;
    }

    public function getEnergyEfficiencyAttribute($value)
    {
        return $this->attributes['energyEfficiency'] ?? $value;
    }

    public function getYearConstructionAttribute($value)
    {
        return $this->attributes['yearConstruction'] ?? $value;
    }

    public function getYearRenovationAttribute($value)
    {
        return $this->attributes['yearRenovation'] ?? $value;
    }

    public function getCoveredVerandaAttribute($value)
    {
        return $this->attributes['coveredVeranda'] ?? $value;
    }

    public function getUncoveredVerandaAttribute($value)
    {
        return $this->attributes['uncoveredVeranda'] ?? $value;
    }

    public function getPublicTransportAttribute($value)
    {
        return $this->attributes['publicTransport'] ?? $value;
    }
}
