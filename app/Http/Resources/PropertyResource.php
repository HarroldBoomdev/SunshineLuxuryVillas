<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        // ✅ Always include ALL attributes loaded from the database
        $attrs = $this->resource->toArray();

        // ✅ Force include missing custom columns (if model has accessors)
        $extra = [
            'floors'                => $this->floors ?? null,
            'parkingSpaces'         => $this->parkingSpaces ?? null,
            'year_construction'     => $this->year_construction ?? null,
            'year_renovation'       => $this->year_renovation ?? null,
            'energyEfficiency'      => $this->energyEfficiency ?? null,
            'owner'                 => $this->owner ?? null,
            'refId'                 => $this->refId ?? null,
            'address'               => $this->address ?? null,
            'labels'                => $this->labels ?? null,
            'image_order'           => $this->image_order ?? null,
            'facilities'            => $this->facilities ?? null,
            'titledeed'             => $this->titledeed ?? null,
            'property_status'       => $this->property_status ?? null,
            'published_at'          => $this->published_at ?? null,
            'external_slug'         => $this->external_slug ?? null,

            // Areas
            'covered_m2'            => $this->covered_m2 ?? null,
            'plot_m2'               => $this->plot_m2 ?? null,
            'roofgarden_m2'         => $this->roofgarden_m2 ?? null,
            'attic_m2'              => $this->attic_m2 ?? null,
            'covered_veranda_m2'    => $this->covered_veranda_m2 ?? null,
            'uncovered_veranda_m2'  => $this->uncovered_veranda_m2 ?? null,
            'garden_m2'             => $this->garden_m2 ?? null,
            'basement_m2'           => $this->basement_m2 ?? null,
            'courtyard_m2'          => $this->courtyard_m2 ?? null,
            'covered_parking_m2'    => $this->covered_parking_m2 ?? null,

            // Distances
            'amenities_km'          => $this->amenities_km ?? null,
            'airport_km'            => $this->airport_km ?? null,
            'sea_km'                => $this->sea_km ?? null,
            'public_transport_km'   => $this->public_transport_km ?? null,
            'schools_km'            => $this->schools_km ?? null,
            'resort_km'             => $this->resort_km ?? null,

            // Land
            'regnum'                => $this->regnum ?? null,
            'plotnum'               => $this->plotnum ?? null,
            'section'               => $this->section ?? null,
            'sheetPlan'             => $this->sheetPlan ?? null,
            'titleDead'             => $this->titleDead ?? null,
            'share'                 => $this->share ?? null,
        ];

        // ✅ Merge extras into attrs
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
