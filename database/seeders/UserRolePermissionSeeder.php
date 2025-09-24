<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Harrold Van Martinez',
                'email' => 'harroldvanmartinez@gmail.com',
                'permissions' => ['view_sales', 'view_listings', 'view_diary', 'change_permissions', 'access_analytics'],
            ],
            [
                'name' => 'Vita Phillips',
                'email' => 'Sales2@sunshineluxuryvillas.com',
                'permissions' => ['view_sales', 'view_listings'],
            ],
            [
                'name' => 'Paul Hann',
                'email' => 'Paul@sunshineluxuryvillas.com',
                'permissions' => ['view_sales', 'view_listings'],
            ],
            [
                'name' => 'Jake Oliver',
                'email' => 'Jake@sunshineluxuryvillas.com',
                'permissions' => ['view_sales', 'view_listings'],
            ],
            [
                'name' => 'Cheryl Hann',
                'email' => 'Cheryl@sunshineluxuryvillas.com',
                'permissions' => ['view_sales'],
            ],
            [
                'name' => 'Iryna Siryk',
                'email' => 'sales1@sunshineluxuryvillas.com',
                'permissions' => ['view_listings', 'view_diary'],
            ],
            [
                'name' => 'Nicole Jacobson',
                'email' => 'team@sunshineluxuryvillas.com',
                'permissions' => ['view_sales', 'view_listings'],
            ],
            [
                'name' => 'Yulia Stanislavchuk',
                'email' => 'yulia@sunshineluxuryvillas.com',
                'permissions' => ['view_listings'],
            ],
            [
                'name' => 'Andriy Stanislavchuk',
                'email' => 'andriy@sunshineluxuryvillas.com',
                'permissions' => ['view_listings'],
            ],
            [
                'name' => 'Scott Toulson',
                'email' => 'Scott@sunshineluxuryvillas.com',
                'permissions' => ['view_sales'],
            ],
            [
                'name' => 'Gabbie Simpson',
                'email' => 'staff@sunshineluxuryvillas.com',
                'permissions' => ['view_sales', 'view_listings'],
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => bcrypt('password')]
            );

            // Sync permissions
            $user->syncPermissions($data['permissions']);
        }

        echo "✅ User roles and permissions seeded.\n";
    }
}
