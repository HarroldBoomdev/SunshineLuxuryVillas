<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportKyeroFeed extends Command
{
    protected $signature = 'import:kyero';
    protected $description = 'Import properties from local Kyero XML file into MySQL database';

    public function handle()
    {
        $this->info("🔄 Loading Kyero feed from local file...");

        libxml_use_internal_errors(true);

        $xmlPath = storage_path('app/kyero.xml');
        $xmlContent = @file_get_contents($xmlPath);

        if (!$xmlContent) {
            $this->error('❌ Failed to load kyero.xml from storage/app.');
            return;
        }

        $xml = simplexml_load_string($xmlContent);
        if (!$xml) {
            $this->error("❌ Invalid XML format.");
            foreach (libxml_get_errors() as $error) {
                $this->error($error->message);
            }
            return;
        }

        $imported = 0;

        foreach ($xml->property as $prop) {
            $externalId = (int)$prop->id;

            // Skip if already exists
            if (DB::table('properties')->where('external_id', $externalId)->exists()) {
                continue;
            }

            $propertyId = DB::table('properties')->insertGetId([
                'external_id'   => $externalId,
                'reference'     => (string)$prop->ref,
                'price'         => (float)$prop->price,
                'property_type' => (string)$prop->type,
                'town'          => (string)$prop->town,
                'province'      => (string)$prop->province,
                'latitude'      => (float)$prop->location->latitude,
                'longitude'     => (float)$prop->location->longitude,
                'beds'          => (int)$prop->beds,
                'baths'         => (int)$prop->baths,
                'pool'          => ((int)$prop->pool === 1),
                'built_area'    => (int)$prop->surface_area->built,
                'plot_area'     => (int)$prop->surface_area->plot,
                'description'   => trim((string)$prop->desc->en),
                'external_url'  => (string)$prop->url,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Insert all image URLs
            foreach ($prop->images->image as $img) {
                DB::table('property_images')->insert([
                    'property_id' => $propertyId,
                    'image_url'   => (string)$img->url,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            $imported++;
        }

        $this->info("✅ Imported $imported new properties.");
    }
}
