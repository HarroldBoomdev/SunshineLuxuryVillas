<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertiesModel;
use Illuminate\Support\Facades\DB;

class KyeroImport extends Command
{
    protected $signature = 'kyero:import {--dry-run}';
    protected $description = 'Map Kyero XML properties and sync key fields into properties table (update + insert + soft-delete)';

    public function handle()
    {
        $relPath  = 'feeds/kyero.xml';
        $fullPath = storage_path('app/' . $relPath);
        $dryRun   = $this->option('dry-run');

        $this->info('=== Kyero Import (LOCAL XML → DB) ===');
        $this->info('Mode: ' . ($dryRun ? 'DRY-RUN (no DB writes)' : 'LIVE (DB will be updated)'));

        // 1) LOAD XML ---------------------------------------------------------
        if (!file_exists($fullPath)) {
            $this->error("XML file not found at: {$fullPath}");
            return 1;
        }

        $this->info("Reading local file: {$relPath}");

        $xmlString = file_get_contents($fullPath);
        if ($xmlString === false || $xmlString === '') {
            $this->error('Failed to read XML file.');
            return 1;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);
        if (!$xml) {
            $this->error('Failed to parse XML.');
            foreach (libxml_get_errors() as $err) {
                $this->line('  - ' . trim($err->message));
            }
            libxml_clear_errors();
            return 1;
        }

        // Try common property node names
        $properties = $xml->xpath('//property');
        if (!$properties || !count($properties)) {
            $properties = $xml->xpath('//listing');
        }

        $total = is_array($properties) ? count($properties) : 0;
        $this->info("Found {$total} properties in feed.");

        if ($total === 0) {
            $this->warn('No properties to process.');
            return 0;
        }

        // 2) BUILD FEED MAP BY REFERENCE --------------------------------------
        $feedMap = []; // [reference => mappedRow]

        foreach ($properties as $node) {
            $row = $this->mapPropertyNode($node);

            $ref = $row['reference'] ?? null;
            if (!$ref) {
                continue; // skip anything without reference
            }

            $feedMap[$ref] = $row;
        }

        $feedCount = count($feedMap);
        $this->info("Mapped {$feedCount} properties with references.");

        if ($feedCount === 0) {
            $this->warn('No properties with reference found. Nothing to sync.');
            return 0;
        }

        $feedRefs = array_keys($feedMap);

        // 3) EXISTING ROWS BY REFERENCE (for updates) -------------------------
        $this->info('Looking up existing properties in DB by reference...');

        /** @var \Illuminate\Support\Collection $existing */
        $existing = PropertiesModel::whereIn('reference', $feedRefs)->get();
        $this->info('Found ' . $existing->count() . ' matching properties in DB.');

        $existingByRef  = $existing->keyBy('reference');
        $existingRefs   = $existingByRef->keys()->all();

        // 4) Determine new / changed / unchanged ------------------------------
        $toUpdate = [];
        $toInsert = [];
        $changedCount = 0;

        foreach ($feedMap as $ref => $row) {
            $prop = $existingByRef->get($ref);

            if ($prop) {
                // Existing row → check for changes
                $changes = [];

                $newBuilt = $row['built_area'];
                $newPlot  = $row['plot_area'];
                $newPrice = $row['price'];
                $newLat   = $row['latitude'];
                $newLng   = $row['longitude'];

                // Compare numerics carefully
                if ($newBuilt !== null && (float)$newBuilt !== (float)$prop->built_area) {
                    $changes['built_area'] = ['old' => $prop->built_area, 'new' => $newBuilt];
                    // mirror into covered_m2 as per earlier mapping
                    $changes['covered_m2'] = ['old' => $prop->covered_m2, 'new' => $newBuilt];
                }

                if ($newPlot !== null && (float)$newPlot !== (float)$prop->plot_area) {
                    $changes['plot_area'] = ['old' => $prop->plot_area, 'new' => $newPlot];
                    $changes['plot_m2']   = ['old' => $prop->plot_m2, 'new' => $newPlot];
                }

                if ($newPrice !== null && (float)$newPrice !== (float)$prop->price) {
                    $changes['price'] = ['old' => $prop->price, 'new' => $newPrice];
                }

                if ($newLat !== null && (float)$newLat !== (float)$prop->latitude) {
                    $changes['latitude'] = ['old' => $prop->latitude, 'new' => $newLat];
                }

                if ($newLng !== null && (float)$newLng !== (float)$prop->longitude) {
                    $changes['longitude'] = ['old' => $prop->longitude, 'new' => $newLng];
                }

                if (!empty($changes)) {
                    $changedCount++;
                    $toUpdate[] = [
                        'model'   => $prop,
                        'ref'     => $ref,
                        'changes' => $changes,
                    ];
                }
            } else {
                // Not in DB → will insert
                $toInsert[] = $row;
            }
        }

        // 5) Determine new vs DB-only refs (for reporting & soft-delete) ------
        // "New" = in feed but not in DB at all
        $newRefs = array_values(array_diff($feedRefs, $existingRefs));

        // "Managed DB" = rows we consider under control of this feed
        // limit to properties pointing at the old SLV propertydetails.aspx URLs
        $managedDbRefs = PropertiesModel::query()
            ->whereNotNull('reference')
            ->whereNotNull('external_url')
            ->where('external_url', 'like', '%sunshineluxuryvillas.com/propertydetails%')
            ->pluck('reference')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // "DB only" = in managed DB set but NOT in current feed → candidates for soft-delete
        $dbOnlyRefs = array_values(array_diff($managedDbRefs, $feedRefs));

        $this->line('');
        $this->info('Feed vs DB comparison:');
        $this->line('  - Total in feed (with reference): ' . count($feedRefs));
        $this->line('  - Total matched in DB: ' . $existing->count());
        $this->line('  - New (in feed but not in DB): ' . count($newRefs));
        $this->line('  - Managed DB only (in DB but not in feed): ' . count($dbOnlyRefs));
        $this->line('  - Properties with changes (price/areas/coords): ' . $changedCount);

        if (!empty($newRefs)) {
            $this->line('');
            $this->line('Sample NEW references (in feed but not in DB):');
            foreach (array_slice($newRefs, 0, 10) as $r) {
                $this->line('  - ' . $r);
            }
        }

        if (!empty($dbOnlyRefs)) {
            $this->line('');
            $this->line('Sample DB-only references (managed in DB but missing in feed):');
            foreach (array_slice($dbOnlyRefs, 0, 10) as $r) {
                $this->line('  - ' . $r);
            }
        }

        // 6) DRY-RUN OUTPUT ---------------------------------------------------
        if ($dryRun) {
            $this->line('');
            $this->line('--- SAMPLE CHANGES (DRY RUN) ---');

            foreach (array_slice($toUpdate, 0, 10) as $item) {
                $this->line('Reference: ' . $item['ref']);
                foreach ($item['changes'] as $field => $delta) {
                    $old = $delta['old'] ?? 'NULL';
                    $new = $delta['new'] ?? 'NULL';
                    $this->line("  - {$field}: {$old} -> {$new}");
                }
            }

            if (!empty($toInsert)) {
                $this->line('');
                $this->line('--- SAMPLE NEW INSERTS (DRY RUN, FIRST 5) ---');
                foreach (array_slice($toInsert, 0, 5) as $row) {
                    $this->line('Reference: ' . ($row['reference'] ?? '(no ref)'));
                    foreach ([
                        'reference',
                        'title',
                        'property_description',
                        'property_type',
                        'location',
                        'town',
                        'region',
                        'country',
                        'built_area',
                        'plot_area',
                        'covered_m2',
                        'plot_m2',
                        'price',
                        'latitude',
                        'longitude',
                        'external_url',
                        'property_status',
                        'published_at',
                    ] as $key) {
                        if (array_key_exists($key, $row)) {
                            $this->line(sprintf('  - %s: %s', $key, var_export($row[$key], true)));
                        }
                    }
                }
            }

            if (!empty($dbOnlyRefs)) {
                $this->line('');
                $this->line('--- SAMPLE SOFT-DELETES (DRY RUN, FIRST 5) ---');
                foreach (array_slice($dbOnlyRefs, 0, 5) as $ref) {
                    $this->line('  - would soft-delete reference: ' . $ref);
                }
            }

            $this->info('=== Kyero Import Finished (DRY-RUN, no DB writes) ===');
            return 0;
        }

        // 7) LIVE UPDATE (NO --dry-run) ---------------------------------------
        $this->line('');
        $this->info('Applying updates to DB...');

        $updatedRows = 0;

        foreach ($toUpdate as $item) {
            /** @var \App\Models\PropertiesModel $prop */
            $prop = $item['model'];
            $changes = $item['changes'];

            if (isset($changes['built_area'])) {
                $prop->built_area = $changes['built_area']['new'];
            }
            if (isset($changes['plot_area'])) {
                $prop->plot_area = $changes['plot_area']['new'];
            }
            if (isset($changes['covered_m2'])) {
                $prop->covered_m2 = $changes['covered_m2']['new'];
            }
            if (isset($changes['plot_m2'])) {
                $prop->plot_m2 = $changes['plot_m2']['new'];
            }
            if (isset($changes['price'])) {
                $prop->price = $changes['price']['new'];
            }
            if (isset($changes['latitude'])) {
                $prop->latitude = $changes['latitude']['new'];
            }
            if (isset($changes['longitude'])) {
                $prop->longitude = $changes['longitude']['new'];
            }

            $prop->save();
            $updatedRows++;
        }

        $this->info("DB update complete. Rows updated: {$updatedRows}");

        // 8) INSERT NEW PROPERTIES -------------------------------------------
        $this->line('');
        $this->info('Inserting new properties from feed (not in DB): ' . count($toInsert));

        $inserted = 0;

        foreach ($toInsert as $row) {
            $prop = new PropertiesModel();

            $prop->reference            = $row['reference'] ?? null;
            $prop->title                = $row['title'] ?? null;
            $prop->property_description = $row['property_description'] ?? null;
            $prop->property_type        = $row['property_type'] ?? null;

            $prop->location             = $row['location'] ?? null;
            $prop->town                 = $row['town'] ?? null;
            $prop->region               = $row['region'] ?? null;
            $prop->country              = $row['country'] ?? null;

            $prop->built_area           = $row['built_area'] ?? null;
            $prop->plot_area            = $row['plot_area'] ?? null;
            $prop->covered_m2           = $row['covered_m2'] ?? null;
            $prop->plot_m2              = $row['plot_m2'] ?? null;

            $prop->price                = $row['price'] ?? null;
            $prop->latitude             = $row['latitude'] ?? null;
            $prop->longitude            = $row['longitude'] ?? null;

            $prop->external_url         = $row['external_url'] ?? null;

            // new properties start as "active" (you can adjust this if needed)
            $prop->property_status      = $row['property_status'] ?? '';

            $prop->published_at         = $row['published_at'] ?? now();

            $prop->save();
            $inserted++;
        }

        $this->info('New properties inserted: ' . $inserted);

        // 9) SOFT-DELETE DB-ONLY PROPERTIES -----------------------------------
        $this->line('');
        $this->info('Soft-deleting properties managed by feed but missing in current XML...');

        $softDeleted = 0;

        if (!empty($dbOnlyRefs)) {
            $propsToSoftDelete = PropertiesModel::whereIn('reference', $dbOnlyRefs)->get();

            foreach ($propsToSoftDelete as $prop) {
                // Option 1 (your choice): property_status + deleted_at
                $prop->property_status = 'inactive';
                $prop->deleted_at      = now();
                $prop->save();
                $softDeleted++;
            }
        }

        $this->info("Soft-deleted properties (marked inactive + deleted_at set): {$softDeleted}");
        $this->info('=== Kyero Import Finished (LIVE) ===');

        return 0;
    }

    /**
     * Map a <property> XML node into a flat array ready for DB insert/update.
     */
    protected function mapPropertyNode(\SimpleXMLElement $node): array
    {
        // --- BASIC FIELDS ----------------------------------------------------
        $ref = (string)($node->ref ?? $node->id ?? '');

        // price may have a currency attribute in Kyero <price currency="EUR">123456</price>
        $priceValue    = null;
        $priceCurrency = 'EUR';

        if (isset($node->price)) {
            $pNode = $node->price;
            $priceCurrency = (string)($pNode['currency'] ?? 'EUR');
            $priceValue    = (string)$pNode;
        }

        if ($priceValue === null || $priceValue === '') {
            // fallback: simple <price>123456</price>
            $priceValue = (string)($node->price ?? '');
        }

        // --- SIZE / AREA -----------------------------------------------------
        $builtArea = null;
        $plotArea  = null;

        if (isset($node->surface_area)) {
            $builtArea = (string)($node->surface_area->built ?? '');
            $plotArea  = (string)($node->surface_area->plot ?? '');
        }

        if (!$builtArea && isset($node->size)) {
            $builtArea = (string)($node->size->built ?? '');
        }
        if (!$plotArea && isset($node->size_plot)) {
            $plotArea = (string)$node->size_plot;
        }

        $builtArea = $this->toNumberOrNull($builtArea);
        $plotArea  = $this->toNumberOrNull($plotArea);

        // --- LOCATION & META -------------------------------------------------
        $title       = (string)($node->description ?? '');
        $description = (string)($node->description ?? '');
        $town        = (string)($node->town ?? '');
        $region      = (string)($node->region ?? '');
        $country     = (string)($node->country ?? '');
        $type        = (string)($node->type ?? '');

        // We use "location" similar to old app: often same as town or area
        $location = $town ?: $region;

        // external URL if present in feed (Kyero usually has <url> or <url_details>)
        $externalUrl = (string)($node->url ?? $node->url_details ?? $node->link ?? '');

        // --- GEO COORDS ------------------------------------------------------
        $lat = null;
        $lng = null;

        if (isset($node->location)) {
            $lat = $this->toNumberOrNull($node->location->latitude ?? null);
            $lng = $this->toNumberOrNull($node->location->longitude ?? null);
        }

        if ($lat === null && isset($node->latitude)) {
            $lat = $this->toNumberOrNull($node->latitude);
        }
        if ($lng === null && isset($node->longitude)) {
            $lng = $this->toNumberOrNull($node->longitude);
        }

        $priceFloat = $priceValue !== null && $priceValue !== ''
            ? (float)str_replace(',', '', $priceValue)
            : null;

        return [
            'reference'            => $ref,
            'price'                => $priceFloat,
            'price_raw'            => $priceValue,
            'currency'             => $priceCurrency,

            'title'                => $title,
            'property_description' => $description,
            'property_type'        => $type,

            'location'             => $location,
            'town'                 => $town,
            'region'               => $region,
            'country'              => $country,

            'built_area'           => $builtArea,
            'plot_area'            => $plotArea,
            'covered_m2'           => $builtArea,
            'plot_m2'              => $plotArea,

            'latitude'             => $lat,
            'longitude'            => $lng,

            'external_url'         => $externalUrl,
            'property_status'      => '', // active/available can be added later if needed
            'published_at'         => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Convert a value to float or null (if empty/invalid).
     */
    protected function toNumberOrNull($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $s = trim((string)$value);
        if ($s === '') {
            return null;
        }

        $s = str_replace(',', '', $s);
        $n = floatval($s);

        return $n > 0 ? $n : null;
    }
}
