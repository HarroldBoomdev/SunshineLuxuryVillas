<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PropertiesImport;

class PropertiesSeeder extends Seeder
{
    public function run()
    {
        Excel::import(new PropertiesImport, public_path('merge.csv'));

        echo "CSV data imported successfully!\n";
    }
}
