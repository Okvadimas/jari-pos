<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name'      => 'Super Admin',
                'slug'      => 'super-admin',
            ],
            [
                'name'      => 'Starter',
                'slug'      => 'starter',
            ],
            [
                'name'      => 'Basic',
                'slug'      => 'basic',
            ],
            [
                'name'      => 'Pro',
                'slug'      => 'pro',
            ],
            [
                'name'      => 'Custom Pro', // Custom Paket
                'slug'      => 'custom-pro',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );
        }
    }
}
