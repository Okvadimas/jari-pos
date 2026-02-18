<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['id' => 1, 'code' => 'FOD', 'name' => 'Pakan Ikan'],
            ['id' => 2, 'code' => 'FBD', 'name' => 'Pakan Burung'],
            ['id' => 3, 'code' => 'FCD', 'name' => 'Pakan Kucing'],
            ['id' => 4, 'code' => 'FDD', 'name' => 'Pakan Anjing'],
            ['id' => 5, 'code' => 'FHD', 'name' => 'Pakan Hamster'],
            ['id' => 6, 'code' => 'AAD', 'name' => 'Aksesoris Akuarium'],
            ['id' => 7, 'code' => 'AAK', 'name' => 'Aksesoris Kandang'],
            ['id' => 8, 'code' => 'OBT', 'name' => 'Obat & Vitamin'],
            ['id' => 9, 'code' => 'PRW', 'name' => 'Perawatan Hewan'],
            ['id' => 10, 'code' => 'LNY', 'name' => 'Lainnya'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['id' => $category['id']],
                [
                    'company_id' => 1,
                    'code' => $category['code'],
                    'name' => $category['name'],
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
