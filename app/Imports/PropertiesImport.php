<?php

namespace App\Imports;

use App\Models\PropertiesModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class PropertiesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Log the CSV row data for debugging
        Log::info('Importing CSV Row:', $row);

        return new PropertiesModel([
            'title' => $this->sanitizeText($row['title'] ?? null),
            'reference' => $this->sanitizeText($row['reference'] ?? null),
            'bedrooms' => isset($row['bedrooms']) ? (int) $row['bedrooms'] : null,
            'bathrooms' => isset($row['bathrooms']) ? (int) $row['bathrooms'] : null,
            'covered' => isset($row['build_size']) ? $this->cleanNumeric($row['build_size']) : null,
            'plot' => isset($row['plot']) ? $this->cleanNumeric($row['plot']) : null,
            'price' => isset($row['price']) && is_numeric(trim($row['price'])) ? $this->cleanNumeric($row['price']) : null,
            'property_description' => $this->sanitizeText($row['property_description'] ?? null),
            'facilities' => $this->parseFacilities($row['additional_features'] ?? null),
            'photos' => $this->parsePhotos($row['photos'] ?? null),
        ]);
    }

    /**
     * Remove unwanted characters from numeric values (e.g., remove "m²" or currency symbols).
     */
    private function cleanNumeric($value)
    {
        return is_numeric($value) ? (float) preg_replace('/[^0-9.]/', '', trim($value)) : null;
    }

    /**
     * Ensure proper text sanitization.
     */
    private function sanitizeText($value)
    {
        return !empty($value) ? trim(strip_tags($value)) : null;
    }

    /**
     * Convert facilities column from string to valid JSON.
     */
    private function parseFacilities($facilities)
    {
        if (empty($facilities)) {
            return json_encode([]); // Store as an empty JSON array
        }

        // Convert single quotes to double quotes for valid JSON
        $facilities = str_replace("'", '"', $facilities);

        // Attempt to decode JSON
        $decoded = json_decode($facilities, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // Manually parse key-value pairs if JSON decoding fails
        $facilitiesArray = [];
        $pairs = explode(',', $facilities);
        foreach ($pairs as $pair) {
            $keyValue = explode(':', $pair, 2);
            if (count($keyValue) == 2) {
                $key = trim($keyValue[0], '" ');
                $value = trim($keyValue[1], '" ');
                $facilitiesArray[$key] = $value;
            }
        }

        return json_encode($facilitiesArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Ensure photos column is stored as a valid JSON array.
     */
    private function parsePhotos($photos)
    {
        if (empty($photos)) {
            return json_encode([]); // Store as an empty JSON array
        }

        $photosArray = array_map('trim', explode(',', $photos));

        return json_encode($photosArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
