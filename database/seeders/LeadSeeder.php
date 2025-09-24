<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $leads = [
            ['month' => 'January', 'year' => 2022, 'location' => 'Paphos', 'source' => null, 'count' => 32],
            ['month' => 'January', 'year' => 2022, 'location' => 'Limassol', 'source' => null, 'count' => 14],
            ['month' => 'January', 'year' => 2022, 'location' => null, 'source' => 'Zoopla', 'count' => 20],
            ['month' => 'January', 'year' => 2022, 'location' => null, 'source' => 'Rightmove', 'count' => 10],
            // repeat this structure for all rows from 2022, 2023, 2024
        ];

        foreach ($leads as $lead) {
            Lead::create($lead);
        }
    }
}
