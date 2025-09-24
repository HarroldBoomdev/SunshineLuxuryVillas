<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ApplicantsTableSeeder extends Seeder
{
    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/applicants.csv'), 'r');
        $csv->setHeaderOffset(0); // Assuming the first row contains headers
        
        foreach ($csv as $record) {
            DB::table('applicants')->insert([
                'Title' => $record['Title'] ?? null,
                'FirstName' => $record['FirstName'],
                'LastName' => $record['LastName'] ?? null,
                'TelephoneEvening' => $record['TelephoneEvening'] ?? null,
                'TelephoneDay' => $record['TelephoneDay'] ?? null,
                'Mobile' => $record['Mobile'] ?? null,
                'Mobile2' => $record['Mobile2'] ?? null,
                'Email' => $record['Email'],
                'Email2' => $record['Email2'] ?? null,
                'Address' => $record['Address'] ?? null,
                'MinimumPrice' => $record['MinimumPrice'] ?? null,
                'MaximumPrice' => $record['MaximumPrice'] ?? null,
                'MinimumBedrooms' => $record['MinimumBedrooms'] ?? null,
                'ReceiveUpdates' => filter_var($record['ReceiveUpdates'], FILTER_VALIDATE_BOOLEAN),
                'Status' => $record['Status'] ?? null,
                'Notes' => $record['Notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }   
}
