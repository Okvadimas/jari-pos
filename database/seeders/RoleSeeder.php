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
        // Clear existing data
        Role::truncate();

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
            Role::create($role);
        }
    }
}
