<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('app/applicants.xlsx');
        $data = Excel::toArray([], $filePath);

        if (empty($data[0]) || count($data[0]) < 2) {
            $this->command->error("Excel file is empty or missing data.");
            return;
        }

        $headers = $data[0][0]; // use original casing
        $rows = array_slice($data[0], 1); // skip header row

        $inserted = 0;

        foreach ($rows as $row) {
            $rowData = [];

            foreach ($headers as $i => $columnName) {
                if ($columnName !== null && $columnName !== '') {
                    $rowData[$columnName] = $row[$i] ?? null;
                }
            }

            DB::table('clients')->insertOrIgnore($rowData);
            $inserted++;
        }

        $this->command->info("✅ Seeded $inserted applicants successfully.");
    }
}
