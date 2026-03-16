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
                'name'      => 'Kelingking',
                'slug'      => 'kelingking',
            ],
            [
                'name'      => 'Jempol',
                'slug'      => 'jempol',
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'created_by' => null,
                    'updated_by' => null,
                ]
            );
        }
    }
}
