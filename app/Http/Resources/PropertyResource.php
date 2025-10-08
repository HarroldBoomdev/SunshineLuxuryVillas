<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        // 1) Start with ALL raw DB attributes (every column except $hidden)
        $attrs = $this->resource->attributesToArray();

        // 2) Alias camelCase <-> snake_case so whichever has data fills the other
        $aliases = [
            // layout / counts
            'livingRooms'        => 'living_rooms',
            'parkingSpaces'      => 'parking_spaces',
            'yearConstruction'   => 'year_construction',
            'yearRenovation'     => 'year_renovation',
            'energyEfficiency'   => 'energy_efficiency',
            'publicTransport'    => 'public_transport',
            'coveredVeranda'     => 'covered_veranda',
            'uncoveredVeranda'   => 'uncovered_veranda',
            'roofGarden'         => 'roof_garden',
            // legal / misc
            'sheetPlan'          => 'sheetplan',
            'titleDead'          => 'title_deed',
            'titledeed'          => 'title_deed',
        ];
        foreach ($aliases as $a => $b) {
            $aHas = array_key_exists($a, $attrs) && $this->filled($attrs[$a]);
            $bHas = array_key_exists($b, $attrs) && $this->filled($attrs[$b]);
            if ($aHas && !$bHas) $attrs[$b] = $attrs[$a];
            if ($bHas && !$aHas) $attrs[$a] = $attrs[$b];
        }

        // 3) Normalize list-like fields to arrays (tolerates double-encoded JSON)
        $attrs['photos']        = $this->normalizeList($attrs['photos']        ?? null);
        $attrs['labels']        = $this->normalizeList($attrs['labels']        ?? null);
        $attrs['features']      = $this->normalizeList($attrs['features']      ?? null);
        $attrs['facilities']    = $this->normalizeList($attrs['facilities']    ?? null);
        $attrs['floor_plans']   = $this->normalizeList($attrs['floor_plans']   ?? null);
        $attrs['youtube_links'] = $this->normalizeList($attrs['youtube_links'] ?? null);

        // 4) Derive cover photo from photos (DB has no 'photo' column)
        $attrs['photo'] = $attrs['photos'][0] ?? null;

        // 5) Location fallback "Town, Province/Region"
        if (empty($attrs['location'])) {
            $town     = $attrs['town']     ?? null;
            $province = $attrs['province'] ?? ($attrs['region'] ?? null);
            $attrs['location'] = trim(($town ? $town . ', ' : '') . ($province ?? ''));
        }

        // 6) Description fallback (use description if property_description empty)
        if (!$this->filled($attrs['property_description'] ?? null) && $this->filled($attrs['description'] ?? null)) {
            $attrs['property_description'] = $attrs['description'];
        }

        // 7) Coords: accept multiple source columns + normalize decimals/ranges
        $latRaw = $attrs['latitude']
            ?? ($attrs['lat']        ?? null)
            ?? ($attrs['latitute']   ?? null)  // common typos
            ?? ($attrs['latittude']  ?? null)
            ?? ($attrs['latitud']    ?? null)
            ?? ($attrs['geo_lat']    ?? null);

        $lngRaw = $attrs['longitude']
            ?? ($attrs['lng']        ?? null)
            ?? ($attrs['long']       ?? null)
            ?? ($attrs['lon']        ?? null)
            ?? ($attrs['longtitude'] ?? null)
            ?? ($attrs['geo_lng']    ?? null);

        $norm = static function ($v) {
            if ($v === null) return null;
            $n = str_replace(',', '.', trim((string)$v));
            return is_numeric($n) ? (float)$n : null;
        };

        $lat = $norm($latRaw);
        $lng = $norm($lngRaw);
        if (!($lat >= -90 && $lat <= 90))   $lat = null;
        if (!($lng >= -180 && $lng <= 180)) $lng = null;

        $attrs['latitude']  = $lat;
        $attrs['longitude'] = $lng;

        // 8) Done
        return ['data' => $attrs];
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
