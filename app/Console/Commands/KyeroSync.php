<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class KyeroSync extends Command
{
    protected $signature = 'kyero:sync {--dry-run}';
    protected $description = 'Sync properties from the Kyero XML feed';

    public function handle()
    {
        $relPath  = 'feeds/kyero.xml';
        $fullPath = storage_path('app/' . $relPath);

        $this->info('=== Kyero Sync (LOCAL FILE) ===');

        // 1) CHECK FILE -----------------------------------------------------------
        if (!file_exists($fullPath)) {
            $this->error("XML file not found at: {$fullPath}");
            $this->error("Make sure storage/app/feeds/kyero.xml exists.");
            return 1;
        }

        $this->info("Reading local file: {$relPath}");

        try {
            $xmlString = file_get_contents($fullPath);
            if ($xmlString === false || $xmlString === '') {
                throw new \RuntimeException('file_get_contents returned empty/false');
            }
        } catch (\Throwable $e) {
            $this->error('Failed to read XML: ' . $e->getMessage());
            return 1;
        }

        // 2) PARSE ---------------------------------------------------------------
        $this->info('Parsing feed (DRY – no DB writes)...');

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

        // Try common Kyero node names
        $properties = $xml->xpath('//property');
        if (!$properties || !count($properties)) {
            $properties = $xml->xpath('//listing');
        }

        $total = is_array($properties) ? count($properties) : 0;
        $this->info("Found {$total} properties in feed.");

        // Preview first few items (ref + price)
        $preview = array_slice($properties, 0, 5);

        foreach ($preview as $idx => $node) {
            // These keys may need tweaking depending on exact XML
            $ref   = (string)($node->ref ?? $node->id ?? '');
            $price = (string)($node->price ?? '');

            // If Kyero uses attributes for currency/value, try to read them
            if (isset($node->price)) {
                $pNode    = $node->price;
                $currency = (string)($pNode['currency'] ?? 'EUR');
                $value    = (string)$pNode;
                if ($value !== '') {
                    $price = "{$currency} {$value}";
                }
            }

            $this->line(sprintf(
                '  #%d  ref=%s  price=%s',
                $idx + 1,
                $ref !== '' ? $ref : '(no ref)',
                $price !== '' ? $price : '(no price)'
            ));
        }

        $this->info('=== Kyero Sync Finished (LOCAL PARSE ONLY, NO DB) ===');

        return 0;
    }
}
