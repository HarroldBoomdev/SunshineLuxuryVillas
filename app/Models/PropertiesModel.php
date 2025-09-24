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
    ];

    protected $casts = [
        'labels'        => 'array',
        'facilities'    => 'array',
        'movedinReady'  => 'date',
        // ⚠️ removed 'photos' => 'array' because we handle it manually
    ];

    /**
     * Accessor: normalize photos column to always return array of URLs.
     */
    public function getPhotosAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        // Already array
        if (is_array($value)) {
            return $value;
        }

        // Stored as JSON with bad quotes (['url1','url2'])
        if (is_string($value)) {
            $normalized = str_replace("'", '"', $value);

            $decoded = json_decode($normalized, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            // Fallback: comma separated list
            return array_filter(array_map('trim', explode(',', $value)));
        }

        return [];
    }

    /**
     * Accessor: cover photo (DB column 'photo' if set, otherwise first from photos).
     */
    public function getPhotoAttribute($value)
    {
        if (is_string($value) && $value !== '') {
            return $value;
        }

        $photos = $this->getPhotosAttribute($this->attributes['photos'] ?? null);

        if (is_array($photos) && isset($photos[0])) {
            if (is_string($photos[0])) {
                return $photos[0];
            }
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
}
