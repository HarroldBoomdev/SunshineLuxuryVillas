<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition()
    {
        return [
            'trace_id' => $this->faker->uuid(),
            'type' => $this->faker->randomElement(['CREATE', 'UPDATE', 'DELETE', 'SYSTEM']),
            'resource_action' => $this->faker->word(),
            'user_name' => $this->faker->name(),
            'user_avatar' => $this->faker->imageUrl(50, 50, 'people', true),
            'date_time' => $this->faker->dateTimeThisYear(),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
