<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class UltraitUploadMedia extends Command
{
    /**
     * Run with: php artisan ultrait:upload-media --limit=5
     */
    protected $signature = 'ultrait:upload-media {--limit=50}';
    protected $description = 'Download media from Ultrait feed and upload to S3.';

    public function handle(): int
    {
        $limit = (int)$this->option('limit');
        $rows = DB::table('ultrait_listing_media')
            ->where('status', 'queued')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            $this->info("No queued media found.");
            return self::SUCCESS;
        }

        $client = new Client(['timeout' => 60]);
        $uploaded = 0;
        $failed = 0;

        foreach ($rows as $row) {
            try {
                // --- download original file ---
                $resp = $client->get($row->src_url, ['http_errors' => false]);
                if ($resp->getStatusCode() !== 200) {
                    throw new \RuntimeException("HTTP " . $resp->getStatusCode());
                }

                $contents = $resp->getBody()->getContents();

                // --- generate S3 key ---
                $ext = pathinfo(parse_url($row->src_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $s3Key = "ultrait/{$row->external_id}/{$row->id}.{$ext}";

                // --- upload to S3 ---
                Storage::disk('s3')->put($s3Key, $contents, 'public');

                // --- mark success in DB ---
                DB::table('ultrait_listing_media')->where('id', $row->id)->update([
                    'status'    => 'uploaded',
                    's3_key'    => $s3Key,
                    'updated_at'=> now(),
                ]);

                $this->info("✅ Uploaded {$row->src_url} -> s3://{$s3Key}");
                $uploaded++;
            } catch (\Throwable $e) {
                DB::table('ultrait_listing_media')->where('id', $row->id)->update([
                    'status'    => 'failed',
                    'error'     => $e->getMessage(),
                    'updated_at'=> now(),
                ]);
                $this->error("❌ Failed {$row->src_url}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("Done. Uploaded={$uploaded}, failed={$failed}");
        return self::SUCCESS;
    }
}
