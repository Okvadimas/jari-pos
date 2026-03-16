<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@jaripos.com',
            'password' => Hash::make('55555'),
            'role_id' => 1, // Super Admin
            'company_id' => 1, // Jari POS Official
            'start_date' => now(),
            'end_date' => now()->addYears(5),
            'email_verified_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        User::create([
            'name' => 'Kelingking',
            'username' => 'kelingking',
            'email' => 'kelingking@jaripos.com',
            'password' => Hash::make('55555'),
            'role_id' => 2, // Kelingking
            'company_id' => 2, // Kelingking Company
            'start_date' => now(),
            'end_date' => now()->addYears(5),
            'email_verified_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        User::create([
            'name' => 'Jempol',
            'username' => 'jempol',
            'email' => 'jempol@jaripos.com',
            'password' => Hash::make('55555'),
            'role_id' => 3, // Jempol
            'company_id' => 3, // Jempol Company
            'start_date' => now(),
            'end_date' => now()->addYears(5),
            'email_verified_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}