<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class AddMissingExcelColumns extends Command
{
    protected $signature = 'excel:sync-columns {file} {table}';
    protected $description = 'Add missing Excel headers as columns to the given MySQL table';

    public function handle()
    {
        $filePath = $this->argument('file');
        $table = $this->argument('table');

        if (!File::exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $this->info("Reading Excel file...");
        $data = Excel::toArray([], $filePath);
        $headers = $data[0][0] ?? [];

        if (empty($headers)) {
            $this->error("Excel file seems to have no headers.");
            return 1;
        }

        $existingColumns = collect(Schema::getColumnListing($table))->map(fn($col) => strtolower($col))->toArray();
        $newColumns = [];

        foreach ($headers as $originalColumnName) {
            $normalized = strtolower($originalColumnName);

            if (!in_array($normalized, $existingColumns)) {
                Schema::table($table, function ($table) use ($originalColumnName) {
                    $table->string($originalColumnName)->nullable();
                });
                $this->info("Added column: $originalColumnName");
            } else {
                $this->line("Column exists: $originalColumnName");
            }
        }

        return 0;
    }
}
