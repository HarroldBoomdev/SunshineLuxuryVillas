<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertiesModel;

class BackfillPropertyMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * You can run it as:
     *  php artisan slv:backfill-property-metrics --dry-run
     */
    protected $signature = 'slv:backfill-property-metrics {--dry-run}';

    /**
     * The console command description.
     */
    protected $description = 'Backfill bedrooms, bathrooms, covered area and plot size from the description field.';

    public function handle()
    {
        $dryRun  = $this->option('dry-run');
        $scanned = 0;
        $updated = 0;

        $this->info('Starting backfill (dry-run: '.($dryRun ? 'YES' : 'NO').')');

        PropertiesModel::whereNotNull('description')
            ->orderBy('id')
            ->chunkById(100, function ($properties) use (&$scanned, &$updated, $dryRun) {
                foreach ($properties as $prop) {
                    $scanned++;
                    $desc = $prop->description;
                    if (!is_string($desc) || trim($desc) === '') {
                        continue;
                    }

                    $updates = [];

                    // Bedrooms
                    if (preg_match('/Bedrooms:\s*(\d+)/i', $desc, $m)) {
                        $bed = (int) $m[1];

                        if (empty($prop->bedrooms)) {
                            $updates['bedrooms'] = $bed;
                        }
                        if (empty($prop->beds)) {
                            $updates['beds'] = $bed;
                        }
                    }

                    // Bathrooms
                    if (preg_match('/Bathrooms:\s*(\d+)/i', $desc, $m)) {
                        $bath = (int) $m[1];

                        if (empty($prop->bathrooms)) {
                            $updates['bathrooms'] = $bath;
                        }
                        if (empty($prop->baths)) {
                            $updates['baths'] = $bath;
                        }
                    }

                    // Covered Internal Area
                    // matches: "Covered Internal Area: 165 m²" or "Covered Internal Area: 165 m2"
                    if (preg_match('/Covered\s+Internal\s+Area:\s*([\d\.]+)/i', $desc, $m)) {
                        $area = (float) $m[1];

                        if (empty($prop->covered_area)) {
                            $updates['covered_area'] = $area;
                        }
                        if (empty($prop->covered_m2)) {
                            $updates['covered_m2'] = $area;
                        }
                    }

                    // Fallback: "Covered Area: 165" (in case some descriptions use that)
                    if (
                        empty($prop->covered_m2)
                        && preg_match('/Covered\s+Area:\s*([\d\.]+)/i', $desc, $m)
                    ) {
                        $area = (float) $m[1];

                        if (empty($prop->covered_area)) {
                            $updates['covered_area'] = $area;
                        }
                        if (empty($prop->covered_m2)) {
                            $updates['covered_m2'] = $area;
                        }
                    }

                    // Plot Size
                    // matches: "Plot Size: 250 m²" or "Plot Size: 250"
                    if (preg_match('/Plot\s+Size:\s*([\d\.]+)/i', $desc, $m)) {
                        $plot = (float) $m[1];

                        if (empty($prop->plot_area)) {
                            $updates['plot_area'] = $plot;
                        }
                        if (empty($prop->plot_m2)) {
                            $updates['plot_m2'] = $plot;
                        }
                    } elseif (
                        // fallback: "Plot: 250 m²"
                        preg_match('/Plot:\s*([\d\.]+)/i', $desc, $m)
                    ) {
                        $plot = (float) $m[1];

                        if (empty($prop->plot_area)) {
                            $updates['plot_area'] = $plot;
                        }
                        if (empty($prop->plot_m2)) {
                            $updates['plot_m2'] = $plot;
                        }
                    }

                    if (empty($updates)) {
                        continue;
                    }

                    $updated++;

                    if ($dryRun) {
                        $this->line(
                            'Would update property ID '.$prop->id.' (external_id '.$prop->external_id.'): '
                            . json_encode($updates)
                        );
                    } else {
                        $prop->fill($updates);
                        $prop->save();
                    }
                }
            });

        $this->info('Scanned '.$scanned.' properties.');
        $this->info('Updated '.$updated.' properties.'.($dryRun ? ' (dry-run only, no DB changes)' : ''));

        return Command::SUCCESS;
    }
}
