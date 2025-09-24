<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        // cover + gallery
        $photos = is_array($this->photos) ? array_values($this->photos) : [];
        $photo  = is_string($this->photo) ? $this->photo : ($photos[0] ?? null);

        // ---- coords (support multiple possible column names) ----
        $latRaw = $this->latitude
            ?? $this->lat
            ?? $this->latitute   // common typo
            ?? $this->latittude  // another typo
            ?? $this->latitud
            ?? $this->geo_lat
            ?? null;

        $lngRaw = $this->longitude
            ?? $this->lng
            ?? $this->long
            ?? $this->lon
            ?? $this->longtitude // common typo
            ?? $this->geo_lng
            ?? null;

        // normalize: accept "34,71197" or "34.71197"
        $norm = static function ($v) {
            if ($v === null) return null;
            $n = str_replace(',', '.', trim((string)$v));
            return is_numeric($n) ? (float)$n : null;
        };

        $lat = $norm($latRaw);
        $lng = $norm($lngRaw);

        // sanity check
        if (!($lat >= -90 && $lat <= 90))   $lat = null;
        if (!($lng >= -180 && $lng <= 180)) $lng = null;

        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'price'                => (string) $this->price,

            'photo'                => $photo,   // single cover URL
            'photos'               => $photos,  // array of URLs

            'bedrooms'             => $this->bedrooms,
            'bathrooms'            => $this->bathrooms,
            'property_type'        => $this->property_type,
            'location'             => $this->location
                                        ?? trim(($this->town ? $this->town . ', ' : '') . ($this->province ?? '')),
            'province'             => $this->province ?? $this->region ?? null,
            'town'                 => $this->town,
            'built_area'           => $this->built_area,
            'plot_area'            => $this->plot_area,
            'reference'            => $this->reference,
            'url'                  => $this->url,
            'property_description' => $this->property_description,

            // ✅ send coords to the frontend
            'latitude'             => $lat,
            'longitude'            => $lng,
        ];
    }
}
