<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;
use Illuminate\Support\Facades\Storage;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/banks.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV file not found at: $path");
            return;
        }

        $rows = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        unset($rows[0]); // remove header

        foreach ($rows as $row) {
            if (count($row) < 3) continue; // skip empties

            $data = array_combine($header, $row);

            Bank::create([
                'reference' => $data['reference'] ?? null,
                'address'   => $data['address'] ?? null,
                'name'      => $data['name'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'mobile'    => $data['mobile'] ?? null,
            ]);
        }

        $this->command->info("Banks table seeded successfully!");
    }
}
