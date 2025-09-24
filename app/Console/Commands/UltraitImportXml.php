<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;

class UltraitImportXml extends Command
{
    /**
     * Run with: php artisan ultrait:import-xml --limit=0
     *   --limit   : max number of <property> nodes to store in ultrait_properties_raw (0 = no limit)
     *   --source  : label for the run (default: xml:full)
     */
    protected $signature = 'ultrait:import-xml
        {--limit=0 : Max properties to capture (0 = all)}
        {--source=xml:full : Run source label}';

    protected $description = 'Download Ultrait XML feed (streamed), snapshot it, and store each <property> as gz+b64 raw XML (staging-safe).';

    public function handle(): int
    {
        $feedUrl   = env('ULTRAIT_XML_URL');
        $ua        = env('ULTRAIT_XML_USER_AGENT', 'SLVDirectPull/1.0');
        $dryRun    = filter_var(env('ULTRAIT_XML_DRY_RUN', true), FILTER_VALIDATE_BOOLEAN);

        if (!$feedUrl) {
            $this->error('ULTRAIT_XML_URL is not set in .env');
            return self::FAILURE;
        }

        // 1) Start run (audit)
        $runId = DB::table('ultrait_runs')->insertGetId([
            'source'      => $this->option('source'),
            'dry_run'     => $dryRun,
            'status'      => 'running',
            'started_at'  => now(),
            'notes'       => json_encode(['url' => $feedUrl]),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $this->info("Run {$runId} started.");

        try {
            // 2) Stream download to a temp file
            $client = new Client([
                'timeout'     => 300,
                'headers'     => [
                    'User-Agent' => $ua,
                    'Accept'     => 'application/xml,text/xml,*/*',
                ],
                'http_errors' => false,
            ]);

            $resp = $client->get($feedUrl, ['stream' => true]);
            $status = $resp->getStatusCode();
            if ($status >= 400) {
                throw new \RuntimeException("Feed fetch failed: HTTP {$status}");
            }

            $ctype   = $resp->getHeaderLine('Content-Type') ?: null;
            $cenc    = $resp->getHeaderLine('Content-Encoding') ?: null;
            $clength = $resp->getHeaderLine('Content-Length') ?: null;

            $tmpPath = 'ultrait/xml/feed-run-' . $runId . '.xml';
            $stream = $resp->getBody();
            Storage::disk('local')->put($tmpPath, ''); // ensure file exists

            $bytes = 0;
            $fh = fopen(Storage::disk('local')->path($tmpPath), 'wb');
            while (!$stream->eof()) {
                $chunk = $stream->read(1024 * 1024); // 1MB chunks
                $bytes += strlen($chunk);
                fwrite($fh, $chunk);
            }
            fclose($fh);

            $this->info("📥 Feed saved to storage/app/{$tmpPath} (" . number_format($bytes) . " bytes).");

            // 3) Snapshot the whole feed (gz+b64)
            $fullPath = Storage::disk('local')->path($tmpPath);
            $filesize = filesize($fullPath);

            $snapshotId = DB::table('ultrait_xml_snapshots')->insertGetId([
                'run_id'           => $runId,
                'feed_url'         => $feedUrl,
                'content_length'   => is_numeric($clength) ? (int)$clength : $filesize,
                'content_type'     => $ctype,
                'content_encoding' => $cenc,
                'body_gzip_b64'    => '',           // leave empty to avoid big packets
                'fetched_at'       => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $this->info("✅ Snapshot metadata stored (ID={$snapshotId}). File kept on disk: {$fullPath}");


            // 4) Stream-parse each <property> and store raw fragment
            $limit = (int)$this->option('limit');
            $stored = 0; $skipped = 0;

            $reader = new \XMLReader();
            $reader->open(Storage::disk('local')->path($tmpPath), null, LIBXML_NOERROR | LIBXML_NOWARNING);

            while ($reader->read()) {
                if ($reader->nodeType === \XMLReader::ELEMENT && strtolower($reader->name) === 'property') {
                    // Build DOM fragment for this <property>
                    $node = $reader->expand();
                    if (!$node) {
                        continue;
                    }
                    $dom = new \DOMDocument('1.0', 'UTF-8');
                    $dom->appendChild($dom->importNode($node, true));
                    $fragmentXml = $dom->saveXML(); // raw <property>…</property>

                    // Extract a few key fields via XPath
                    $xp = new \DOMXPath($dom);
                    $externalId = self::firstText($xp, '//id');
                    $ref        = self::firstText($xp, '//ref');
                    $dateStr    = self::firstText($xp, '//date');
                    $dateUpdStr = self::firstText($xp, '//date_updated|//updated');

                    if (!$externalId) {
                        // No id? Keep but mark skipped count.
                        $skipped++;
                        continue;
                    }

                    $hash = hash('sha256', $fragmentXml);
                    $xmlDate       = self::parseStamp($dateStr);
                    $xmlDateUpdate = self::parseStamp($dateUpdStr);

                    // gz+b64 the fragment
                    $fragB64 = base64_encode(gzencode($fragmentXml, 6));

                    // Insert if new content (id+hash unique)
                    try {
                        DB::table('ultrait_properties_raw')->insert([
                            'run_id'                 => $runId,
                            'external_id'            => (string)$externalId,
                            'reference_code'         => $ref ?: null,
                            'xml_date'               => $xmlDate,
                            'xml_date_updated'       => $xmlDateUpdate,
                            'hash'                   => $hash,
                            'property_xml_gzip_b64'  => $fragB64,
                            'meta'                   => json_encode([], JSON_UNESCAPED_SLASHES),
                            'created_at'             => now(),
                            'updated_at'             => now(),
                        ]);
                        $stored++;
                    } catch (\Throwable $e) {
                        // Likely unique constraint hit => same id+hash already stored.
                        $skipped++;
                    }

                    if ($limit > 0 && $stored >= $limit) {
                        break;
                    }
                }
            }
            $reader->close();

            // 5) Finish run
            DB::table('ultrait_runs')->where('id', $runId)->update([
                'finished_at'  => now(),
                'status'       => 'success',
                'new_count'    => $stored,
                'update_count' => 0,
                'image_count'  => 0,
                'updated_at'   => now(),
            ]);

            $this->info("Done. properties stored={$stored}, skipped={$skipped}");
            return self::SUCCESS;

        } catch (\Throwable $e) {
            DB::table('ultrait_runs')->where('id', $runId)->update([
                'finished_at' => now(),
                'status'      => 'failed',
                'error'       => $e->getMessage(),
                'updated_at'  => now(),
            ]);
            $this->error("FAILED: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    private static function firstText(\DOMXPath $xp, string $path): ?string
    {
        $n = $xp->query($path);
        if (!$n || $n->length === 0) return null;
        return trim($n->item(0)->textContent);
    }

    private static function parseStamp(?string $s): ?string
    {
        if (!$s) return null;
        try {
            return Carbon::parse($s)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
