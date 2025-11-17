<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertiesModel as Property; // ⬅ change if your model is different

class ImportSurfaceAreasFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *  php artisan surface-areas:import --dry-run
     *  php artisan surface-areas:import
     */
    protected $signature = 'surface-areas:import {--dry-run : Do not write to DB, just show what would change}';

    /**
     * The console command description.
     */
    protected $description = 'Import built_area and plot_area from CSV and update properties table';

    public function handle()
    {
        // CSV path
        $csvPath = storage_path('app/import/surface_areas.csv');

        if (! file_exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return 1;
        }

        $this->info("Loading CSV from: {$csvPath}");

        $dryRun   = (bool) $this->option('dry-run');
        $updated  = 0;
        $skipped  = 0;
        $notFound = 0;

        if (($handle = fopen($csvPath, 'r')) === false) {
            $this->error("Unable to open CSV file.");
            return 1;
        }

        // Read header row
        $header = fgetcsv($handle);
        if (! $header) {
            $this->error('CSV header row is missing.');
            fclose($handle);
            return 1;
        }

        // Normalize header to lower snake_case
        $header = array_map(function ($h) {
            return strtolower(trim($h));
        }, $header);

        // Helper to map row to assoc array
        $getRowAssoc = function ($row) use ($header) {
            $assoc = [];
            foreach ($header as $i => $key) {
                $assoc[$key] = $row[$i] ?? null;
            }
            return $assoc;
        };

        while (($row = fgetcsv($handle)) !== false) {
            $data = $getRowAssoc($row);

            $externalId = trim((string)($data['external_id'] ?? ''));
            $reference  = trim((string)($data['reference'] ?? ''));
            $built      = trim((string)($data['built_area'] ?? ''));
            $plot       = trim((string)($data['plot_area'] ?? ''));

            // Nothing to update
            if ($built === '' && $plot === '') {
                $skipped++;
                continue;
            }

            // Find property by external_id first, then reference
            $query = Property::query();

            if ($externalId !== '') {
                $query->where('external_id', $externalId);
            } elseif ($reference !== '') {
                $query->where('reference', $reference);
            } else {
                $notFound++;
                $this->line("[WARN] CSV row has no external_id or reference → skipping");
                continue;
            }

            /** @var \App\Models\PropertiesModel|null $property */
            $property = $query->first();

            if (! $property) {
                $notFound++;
                $this->line("[WARN] No DB match for external_id='{$externalId}' reference='{$reference}'");
                continue;
            }

            $changes = [];

            if ($built !== '' && (string)$property->built_area !== $built) {
                $changes['built_area'] = $built;
            }

            if ($plot !== '' && (string)$property->plot_area !== $plot) {
                $changes['plot_area'] = $plot;
            }

            if (empty($changes)) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->line(sprintf(
                    "[DRY RUN] Would update property id=%s ref=%s external_id=%s built_area=%s plot_area=%s",
                    $property->id,
                    $property->reference,
                    $externalId ?: 'NULL',
                    $changes['built_area'] ?? $property->built_area,
                    $changes['plot_area'] ?? $property->plot_area
                ));
            } else {
                foreach ($changes as $field => $value) {
                    $property->{$field} = $value;
                }
                $property->save();

                $this->line(sprintf(
                    "Updated property id=%s ref=%s external_id=%s built_area=%s plot_area=%s",
                    $property->id,
                    $property->reference,
                    $externalId ?: 'NULL',
                    $property->built_area,
                    $property->plot_area
                ));
            }

            $updated++;
        }

        fclose($handle);

        $this->info("Done.");
        $this->info("  Updated : {$updated}");
        $this->info("  Skipped : {$skipped}");
        $this->info("  Not found (no DB match) : {$notFound}");

        if ($dryRun) {
            $this->warn('This was a DRY RUN. No database changes were made.');
        }

        return 0;
    }
}
