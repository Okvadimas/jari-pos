<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Jari POS Official',
            'email' => 'company@jaripos.com',
            'phone' => '08123456789',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'logo' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Company::create([
            'name' => 'Demo Company',
            'email' => 'demo@jaripos.com',
            'phone' => '08987654321',
            'address' => 'Jl. Demo No. 456, Bandung',
            'logo' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
