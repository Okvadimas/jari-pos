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
                'nama'      => 'Super Admin',
                'slug'      => 'super-admin',
                'insert_at' => now(),
                'insert_by' => 'system',
                'status'    => 'active',
            ],
            [
                'nama'      => 'Starter',
                'slug'      => 'starter',
                'insert_at' => now(),
                'insert_by' => 'system',
                'status'    => 'active',
            ],
            [
                'nama'      => 'Basic',
                'slug'      => 'basic',
                'insert_at' => now(),
                'insert_by' => 'system',
                'status'    => 'active',
            ],
            [
                'nama'      => 'Pro',
                'slug'      => 'pro',
                'insert_at' => now(),
                'insert_by' => 'system',
                'status'    => 'active',
            ],
            [
                'nama'      => 'Custom Nexa', // Custom Paket
                'slug'      => 'custom-nexa',
                'insert_at' => now(),
                'insert_by' => 'system',
                'status'    => 'active',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
