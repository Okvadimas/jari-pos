<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Affiliator;

class AffiliatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Affiliator::create([
            'name' => 'Jari POS Official',
            'email' => 'official@jaripos.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'code' => 'JARI20',
            'discount_rate' => 20.00,
            'commission_rate' => 20.00,
            'is_active' => true,
        ]);
        
        Affiliator::create([
            'name' => 'Kasir Pintar Affiliate',
            'email' => 'affiliate@kasirpintar.co.id',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'code' => 'KASIRPINTAR',
            'discount_rate' => 15.00,
            'commission_rate' => 25.00,
            'is_active' => true,
        ]);
    }
}
