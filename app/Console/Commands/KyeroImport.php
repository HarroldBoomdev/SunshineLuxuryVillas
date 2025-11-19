<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertiesModel; // <-- adjust this if your model is named differently

class KyeroImport extends Command
{
    protected $signature = 'kyero:import {--dry-run}';
    protected $description = 'Map Kyero XML properties and sync key fields into properties table';

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

        $this->info('Mapped ' . count($feedMap) . ' properties with references.');

        if (empty($feedMap)) {
            $this->warn('No properties with reference found. Nothing to sync.');
            return 0;
        }

        // 3) LOOK UP MATCHING DB ROWS -----------------------------------------
        $refs = array_keys($feedMap);

        $this->info('Looking up existing properties in DB by reference...');

        $existing = PropertiesModel::whereIn('reference', $refs)->get();

        $this->info('Found ' . $existing->count() . ' matching properties in DB.');

        $toUpdate     = [];
        $changedCount = 0;

        foreach ($existing as $prop) {
            $ref     = $prop->reference;
            $feedRow = $feedMap[$ref] ?? null;
            if (!$feedRow) {
                continue;
            }

            // New values from feed
            $newBuilt = $feedRow['built_area'];
            $newPlot  = $feedRow['plot_area'];
            $newPrice = $feedRow['price']; // we will NOT apply this; kept only for debug if needed

            // Old values from DB
            $oldBuilt = $prop->built_area;
            $oldPlot  = $prop->plot_area;
            $oldPrice = $prop->price;

            $dirty   = false;
            $changes = [];

            // Only update if feed has a non-null built_area
            if ($newBuilt !== null && (float)$newBuilt !== (float)$oldBuilt) {
                $changes['built_area'] = ['old' => $oldBuilt, 'new' => $newBuilt];
                $dirty = true;
            }

            // Only update if feed has a non-null plot_area
            if ($newPlot !== null && (float)$newPlot !== (float)$oldPlot) {
                $changes['plot_area'] = ['old' => $oldPlot, 'new' => $newPlot];
                $dirty = true;
            }

            // 🔒 Price syncing disabled — we keep this logic commented out on purpose.
            // If in the future you want to turn it on, just uncomment this block
            // AND the saving block below.
            //
            // if ($newPrice !== null && (float)$newPrice !== (float)$oldPrice) {
            //     $changes['price'] = ['old' => $oldPrice, 'new' => $newPrice];
            //     $dirty = true;
            // }

            if ($dirty) {
                $changedCount++;
                $toUpdate[] = [
                    'model'   => $prop,
                    'ref'     => $ref,
                    'changes' => $changes,
                ];
            }
        }

        $this->info("Properties with changes: {$changedCount}");

        // 4) DRY-RUN OUTPUT ----------------------------------------------------
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

            $this->info('=== Kyero Import Finished (DRY-RUN, no DB writes) ===');
            return 0;
        }

        // 5) LIVE UPDATE (NO --dry-run) ---------------------------------------
        $this->line('');
        $this->info('Applying updates to DB...');

        $updatedRows = 0;

        foreach ($toUpdate as $item) {
            /** @var \App\Models\PropertiesModel $prop */
            $prop = $item['model'];

            if (isset($item['changes']['built_area'])) {
                $prop->built_area = $item['changes']['built_area']['new'];
            }
            if (isset($item['changes']['plot_area'])) {
                $prop->plot_area = $item['changes']['plot_area']['new'];
            }

            // 🔒 Price updating disabled — intentionally commented.
            // if (isset($item['changes']['price'])) {
            //     $prop->price = $item['changes']['price']['new'];
            // }

            $prop->save();
            $updatedRows++;
        }

        $this->info("DB update complete. Rows updated: {$updatedRows}");
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

        // price can be nested with currency attribute in Kyero
        $priceValue    = null;
        $priceCurrency = 'EUR';

        if (isset($node->price)) {
            $pNode         = $node->price;
            $priceCurrency = (string)($pNode['currency'] ?? 'EUR');
            $priceValue    = (string)$pNode;
        }

        if ($priceValue === null || $priceValue === '') {
            // fallback if price is simple node <price>123456</price>
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

        // --- LOCATION FIELDS -------------------------------------------------
        $title   = (string)($node->description ?? '');
        $town    = (string)($node->town ?? '');
        $region  = (string)($node->region ?? '');
        $country = (string)($node->country ?? '');
        $type    = (string)($node->type ?? '');

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

        return [
            'reference'     => $ref,
            'price'         => $priceValue !== null ? (float)$priceValue : null,
            'price_raw'     => $priceValue,
            'currency'      => $priceCurrency,
            'title'         => $title,
            'property_type' => $type,
            'town'          => $town,
            'region'        => $region,
            'country'       => $country,
            'built_area'    => $builtArea,
            'plot_area'     => $plotArea,
            'latitude'      => $lat,
            'longitude'     => $lng,
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
