<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UltraitParseXmlRaw extends Command
{
    /**
     * Run with: php artisan ultrait:parse-xml-raw --limit=0
     *   --limit : max rows to parse from ultrait_properties_raw (0 = all)
     */
    protected $signature = 'ultrait:parse-xml-raw {--limit=0}';
    protected $description = 'Parse raw <property> fragments into ultrait_listings_staging (idempotent upserts).';

    public function handle(): int
    {
        $limit = (int)$this->option('limit');
        $q = DB::table('ultrait_properties_raw')->orderBy('id');
        if ($limit > 0) $q->limit($limit);
        $rows = $q->get();

        $parsed = 0; $updated = 0; $inserted = 0; $skipped = 0;

        foreach ($rows as $row) {
            $xml = @gzdecode(base64_decode($row->property_xml_gzip_b64));
            if (!$xml) { $skipped++; continue; }

            $dom = new \DOMDocument('1.0', 'UTF-8');
            @$dom->loadXML($xml);
            $xp = new \DOMXPath($dom);

            $id   = self::txt($xp, '//id');
            if (!$id) { $skipped++; continue; }

            // core fields
            $ref  = self::txt($xp, '//ref');
            $status = strtolower(self::txt($xp, '//price_freq') ?: 'unknown'); // sale/longlet/shortlet
            $price = self::num(self::txt($xp, '//price'));
            $curr  = self::txt($xp, '//currency|//price_currency');

            $type  = self::txt($xp, '//type');
            $beds  = self::int(self::txt($xp, '//beds'));
            $baths = self::int(self::txt($xp, '//baths'));
            $built = self::int(self::txt($xp, '//built|//build_size'));
            $plot  = self::int(self::txt($xp, '//plot|//plot_size'));

            $country  = self::txt($xp, '//country');
            $province = self::txt($xp, '//province|//region');
            $town     = self::txt($xp, '//town|//area');

            $lat = self::dec(self::txt($xp, '//latitude'));
            $lon = self::dec(self::txt($xp, '//longitude'));

            $url = self::txt($xp, '//url');
            $date = self::stamp(self::txt($xp, '//date'));
            $dateUpd = self::stamp(self::txt($xp, '//date_updated|//updated'));

            // digest over key fields
            $digest = hash('sha256', json_encode([
                $ref,$status,$price,$curr,$type,$beds,$baths,$built,$plot,$country,$province,$town,$lat,$lon,$url,$date,$dateUpd
            ]));

            $payload = [
                'reference_code'     => $ref ?: null,
                'status'             => in_array($status, ['sale','longlet','shortlet']) ? $status : 'unknown',
                'price'              => $price,
                'currency'           => $curr ?: null,
                'type'               => $type ?: null,
                'beds'               => $beds,
                'baths'              => $baths,
                'built'              => $built,
                'plot'               => $plot,
                'country'            => $country ?: null,
                'province'           => $province ?: null,
                'town'               => $town ?: null,
                'latitude'           => $lat,
                'longitude'          => $lon,
                'detail_url'         => $url ?: null,
                'xml_date'           => $date,
                'xml_date_updated'   => $dateUpd,
                'digest'             => $digest,
                'updated_at'         => now(),
            ];

            // upsert by external_id
            $existing = DB::table('ultrait_listings_staging')->where('external_id', $id)->first();
            if ($existing) {
                if ($existing->digest !== $digest) {
                    DB::table('ultrait_listings_staging')->where('id', $existing->id)->update($payload);
                    $updated++;
                }
            } else {
                DB::table('ultrait_listings_staging')->insert(array_merge([
                    'external_id' => (string)$id,
                    'created_at'  => now(),
                ], $payload));
                $inserted++;
            }

            $parsed++;
        }

        $this->info("Parsed=$parsed, inserted=$inserted, updated=$updated, skipped=$skipped");
        return self::SUCCESS;
    }

    private static function txt(\DOMXPath $xp, string $path): ?string {
        $n = $xp->query($path); if (!$n || !$n->length) return null;
        return trim($n->item(0)->textContent);
    }
    private static function int(?string $s): ?int {
        if ($s === null || $s === '') return null;
        if (!preg_match('/-?\d+/', $s, $m)) return null;
        return (int)$m[0];
    }
    private static function num(?string $s): ?float {
        if ($s === null || $s === '') return null;
        $c = preg_replace('/[^\d\.\-]/', '', str_replace(',', '', $s));
        return $c === '' ? null : (float)$c;
    }
    private static function dec(?string $s): ?float {
        if ($s === null || $s === '') return null;
        if (!is_numeric($s)) return null;
        return (float)$s;
    }
    private static function stamp(?string $s): ?string {
        if (!$s) return null;
        try { return Carbon::parse($s)->format('Y-m-d H:i:s'); } catch (\Throwable $e) { return null; }
    }
}
