<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertiesModel;
use Illuminate\Support\Facades\Schema;

class ImportProperties extends Command
{
    protected $signature = 'import:properties';
    protected $description = 'Import properties from CSV into the database';

    public function handle()
    {
        $filePath = storage_path('app/import/properties.csv');

        if (!file_exists($filePath)) {
            $this->error("❌ CSV file not found at: $filePath");
            return;
        }

        $rows = array_map('str_getcsv', file($filePath));
        $header = array_map('trim', array_shift($rows));

        $skipped = 0;
        $imported = 0;

        foreach ($rows as $index => $row) {
            if (count($row) !== count($header)) {
                $this->warn("⚠️ Skipping row $index — column count mismatch");
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);
            $property = new PropertiesModel();

            $fieldsMap = [
                'reference' => 'reference',
                'price_x' => 'price',
                'price_freq' => 'price_freq',
                'type' => 'property_type',
                'town' => 'location',
                'province' => 'province',
                'latitude' => 'latitude',
                'longitude' => 'longitude',
                'built_area' => 'built_area',
                'plot_area' => 'plot_area',
                'description_x' => 'property_description',
                'description_y' => 'property_description_alt',
                'vendor_name' => 'vendor_name',
                'telephone' => 'telephone',
                'mobile' => 'mobile',
                'title' => 'title',
                'bedrooms' => 'bedrooms',
                'bathrooms' => 'bathrooms',
                'build' => 'year_construction',
                'pool_y' => 'pool',
                'additional_features' => 'features',
            ];

            $valid = true;

            foreach ($fieldsMap as $csvKey => $dbColumn) {
                if (!isset($data[$csvKey]) || !Schema::hasColumn('properties', $dbColumn)) {
                    continue;
                }

                $value = trim($data[$csvKey]);

                // Numeric fields (validate strictly)
                if (in_array($dbColumn, ['price', 'latitude', 'longitude', 'built_area', 'plot_area'])) {
                    $clean = preg_replace('/[^0-9.\-]/', '', $value);
                    if ($clean === '' || !is_numeric($clean)) {
                        $this->warn("❌ Row $index: Invalid number for '$dbColumn' → '$value' — skipping row");
                        $valid = false;
                        break;
                    }
                    $property->$dbColumn = $clean;
                }
                // Integer fields
                elseif (in_array($dbColumn, ['bedrooms', 'bathrooms'])) {
                    $property->$dbColumn = is_numeric($value) ? (int) $value : null;
                }
                // All others as strings
                else {
                    $property->$dbColumn = $value ?: null;
                }
            }

            if (!$valid) {
                $skipped++;
                continue;
            }

            // Handle property_images
            if (isset($data['property_images']) && Schema::hasColumn('properties', 'photos')) {
                $imageList = array_map('trim', explode(',', $data['property_images']));
                $property->photos = json_encode($imageList, JSON_UNESCAPED_SLASHES);
            }

            try {
                $property->save();
                $imported++;
            } catch (\Throwable $e) {
                $this->warn("❌ Row $index skipped — DB error: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->info("✅ Import complete.");
        $this->info("✔️ Imported: $imported row(s)");
        $this->info("❌ Skipped: $skipped row(s)");
    }
}
