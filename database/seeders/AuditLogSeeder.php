<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run()
    {
        AuditLog::factory(50)->create(); // Assuming a factory exists
    }
}
