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
            'start_date' => now(),
            'end_date' => now()->addYears(5),
        ]);

        User::create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@jaripos.com',
            'password' => Hash::make('00000'),
            'role_id' => 2, // Starter
            'start_date' => now(),
            'end_date' => now()->addYears(5),
        ]);
    }
}