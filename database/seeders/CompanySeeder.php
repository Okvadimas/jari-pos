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
            'code' => 'JPO',
            'name' => 'Jari POS Official',
            'email' => 'company@jaripos.com',
            'phone' => '08123456789',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'logo' => null,
            'created_by' => null,
            'updated_by' => null,
        ]);

        Company::create([
            'code' => 'KC',
            'name' => 'Kelingking Company',
            'email' => 'kelingking@jaripos.com',
            'phone' => '08987654321',
            'address' => 'Jl. Kelingking No. 456, Bandung',
            'logo' => null,
            'created_by' => null,
            'updated_by' => null,
        ]);

        Company::create([
            'code' => 'JC',
            'name' => 'Jempol Company',
            'email' => 'jempol@jaripos.com',
            'phone' => '08987654321',
            'address' => 'Jl. Jempol No. 456, Bandung',
            'logo' => null,
            'created_by' => null,
            'updated_by' => null,
        ]);
    }
}
