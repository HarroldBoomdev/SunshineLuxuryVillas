<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(AdminSeeder::class);
        // $this->call(PropertiesSeeder::class);
        // $this->call(ApplicantsSeeder::class);
        // $this->call(ApplicantsTableSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // $this->call(LeadSeeder::class);
         $this->call([
            Leads2022Seeder::class,
        ]);
    }
}
