<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UltraitParseXmlTextsMedia extends Command
{
    /**
     * Run with: php artisan ultrait:parse-xml-texts-media --limit=0
     *  --limit : max raw rows to process (0 = all)
     */
    protected $signature = 'ultrait:parse-xml-texts-media {--limit=0}';
    protected $description = 'Extract <desc lang=".."> and media (images/floorplans/videos) into staging tables.';

    public function handle(): int
    {
        $limit = (int)$this->option('limit');
        $q = DB::table('ultrait_properties_raw')->orderBy('id');
        if ($limit > 0) $q->limit($limit);
        $rows = $q->get();

        $textUpserts = 0;
        $mediaInserts = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $xml = @gzdecode(base64_decode($row->property_xml_gzip_b64));
            if (!$xml) { $skipped++; continue; }

            $dom = new \DOMDocument('1.0', 'UTF-8');
            @$dom->loadXML($xml);
            $xp = new \DOMXPath($dom);

            $externalId = self::txt($xp, '//id');
            if (!$externalId) { $skipped++; continue; }

            // --- TEXTS: <desc lang=".."> ---
            $descNodes = $xp->query('//desc[@lang]');
            if ($descNodes && $descNodes->length) {
                foreach ($descNodes as $n) {
                    $lang = strtolower($n->getAttribute('lang') ?: 'en');
                    $text = trim($n->textContent);

                    // upsert by (external_id, lang, kind=desc)
                    $existing = DB::table('ultrait_listing_texts')
                        ->where(['external_id' => $externalId, 'lang' => $lang, 'kind' => 'desc'])
                        ->first();

                    if ($existing) {
                        DB::table('ultrait_listing_texts')
                            ->where('id', $existing->id)
                            ->update(['text' => $text, 'updated_at' => now()]);
                    } else {
                        DB::table('ultrait_listing_texts')->insert([
                            'external_id' => $externalId,
                            'lang'        => $lang,
                            'kind'        => 'desc',
                            'text'        => $text,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
                    $textUpserts++;
                }
            }

            // --- MEDIA: images (and optional floorplans/videos if present) ---
            // Images
            $imgs = $xp->query('//images//image//url | //images//image/url | //image/url');
            $ordinal = 0;
            if ($imgs && $imgs->length) {
                foreach ($imgs as $u) {
                    $url = trim($u->textContent);
                    if ($url === '') continue;

                    // insert if not already present for this listing+url
                    $exists = DB::table('ultrait_listing_media')
                        ->where(['external_id' => $externalId, 'kind' => 'image', 'src_url' => $url])
                        ->exists();
                    if (!$exists) {
                        DB::table('ultrait_listing_media')->insert([
                            'external_id' => $externalId,
                            'kind'        => 'image',
                            'src_url'     => $url,
                            'ordinal'     => $ordinal++,
                            'status'      => 'queued',
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                        $mediaInserts++;
                    }
                }
            }

            // TODO (later): floorplans/videos/tours if your feed includes them
            // e.g., //floorplans//url, //videos//url, //tours//url
        }

        $this->info("Texts upserted={$textUpserts}, media inserted={$mediaInserts}, skipped={$skipped}");
        return self::SUCCESS;
    }

    private static function txt(\DOMXPath $xp, string $path): ?string {
        $n = $xp->query($path); if (!$n || !$n->length) return null;
        return trim($n->item(0)->textContent);
    }
}
