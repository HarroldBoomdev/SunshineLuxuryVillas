<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        // ✅ Include all loaded DB attributes
        $attrs = $this->resource->toArray();

        // ✅ Fallback for built_area & plot_area
        $attrs['built_area'] = $this->built_area ?? $this->covered_m2;
        $attrs['plot_area']  = $this->plot_area  ?? $this->plot_m2;

        // ✅ Always expose latitude/longitude from DB raw values (bulletproof)
        $lat = $this->resource->getRawOriginal('latitude');
        $lng = $this->resource->getRawOriginal('longitude');

        // If for any reason rawOriginal is null but attribute exists (edge cases)
        if ($lat === null) $lat = $this->resource->getAttribute('latitude');
        if ($lng === null) $lng = $this->resource->getAttribute('longitude');

        // ✅ Extra fields (kept from your existing setup)
        $extra = [
            'floors'            => $this->floors,
            'parkingSpaces'     => $this->parkingSpaces,
            'year_construction' => $this->year_construction,
            'year_renovation'   => $this->year_renovation,
            'energyEfficiency'  => $this->energyEfficiency,
            'owner'             => $this->owner,
            'refId'             => $this->refId,
            'address'           => $this->address,
            'labels'            => $this->labels,
            'image_order'       => $this->image_order,
            'facilities'        => $this->facilities,
            'titledeed'         => $this->titledeed,
            'property_status'   => $this->property_status,
            'published_at'      => $this->published_at,
            'external_slug'     => $this->external_slug,

            // Areas
            'covered_m2'            => $this->covered_m2,
            'plot_m2'               => $this->plot_m2,
            'roofgarden_m2'         => $this->roofgarden_m2,
            'attic_m2'              => $this->attic_m2,
            'covered_veranda_m2'    => $this->covered_veranda_m2,
            'uncovered_veranda_m2'  => $this->uncovered_veranda_m2,
            'garden_m2'             => $this->garden_m2,
            'basement_m2'           => $this->basement_m2,
            'courtyard_m2'          => $this->courtyard_m2,
            'covered_parking_m2'    => $this->covered_parking_m2,

            // Distances
            'amenities_km'        => $this->amenities_km,
            'airport_km'          => $this->airport_km,
            'sea_km'              => $this->sea_km,
            'public_transport_km' => $this->public_transport_km,
            'schools_km'          => $this->schools_km,
            'resort_km'           => $this->resort_km,

            // Land
            'regnum'    => $this->regnum,
            'plotnum'   => $this->plotnum,
            'section'   => $this->section,
            'sheetPlan' => $this->sheetPlan,
            'titleDead' => $this->titleDead,
            'share'     => $this->share,

            // ✅ Geo
            'latitude'  => $lat,
            'longitude' => $lng,
        ];

        return array_merge($attrs, $extra);
    }

    /** Helper: treat empty strings as not filled */
    private function filled($v): bool
    {
        if ($v === null) return false;
        if (is_string($v)) return trim($v) !== '';
        return true;
    }

    /**
     * Turn a mixed list value (json/double-encoded json/csv/newline/array/null) into a clean array of strings.
     */
    private function normalizeList($v): array
    {
        if ($v === null) return [];
        if (is_array($v)) {
            return array_values(array_filter(array_map(fn($x) => trim((string)$x), $v)));
        }

        $s = trim((string)$v);

        // 1) try plain JSON
        $j = json_decode($s, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($j)) {
            return array_values(array_filter(array_map(fn($x) => trim((string)$x), $j)));
        }

        // 2) double-encoded JSON -> unescape & decode again
        $s2 = stripcslashes(trim($s, "\"'"));
        $j2 = json_decode($s2, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($j2)) {
            return array_values(array_filter(array_map(fn($x) => trim((string)$x), $j2)));
        }

        // 3) last resort: split on delimiters
        return array_values(array_filter(array_map('trim', preg_split('/,|;|\n|·|•/', $s))));
    }
}
