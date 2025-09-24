<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SalePropertiesSeeder extends Seeder
{
    public function run()
    {
        $filePath = storage_path('app/sale_properties.csv');

        // Open CSV file
        $handle = fopen($filePath, "r");

        if ($handle !== FALSE) {
            // Skip the first row (headers)
            fgetcsv($handle);

            $data = [];

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = [
                    'property_id'    => $row[0],
                    'reference'      => $row[1],
                    'location'       => $row[2],
                    'property_type'  => $row[3],
                    'bedrooms'       => is_numeric($row[4]) ? (int)$row[4] : null,
                    'price'          => $row[5],
                    'pool'           => $row[6] == '✔' ? 1 : 0,
                    'featured'       => $row[7] == '✔' ? 1 : 0,
                    'live'           => $row[8] == '✔' ? 1 : 0,
                    'preview_image'  => '/uploads/properties/' . $row[0] . '.jpg',
                    'created_at'     => now(),
                    'updated_at'     => now()
                ];

                // Insert in batches of 500 for performance
                if (count($data) >= 500) {
                    DB::table('sale_properties')->insert($data);
                    $data = [];
                }
            }

            // Insert remaining data
            if (!empty($data)) {
                DB::table('sale_properties')->insert($data);
            }

            fclose($handle);
        }
    }
}
