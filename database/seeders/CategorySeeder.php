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
            ['id' => 1, 'name' => 'Pakan Ikan'],
            ['id' => 2, 'name' => 'Pakan Burung'],
            ['id' => 3, 'name' => 'Pakan Kucing'],
            ['id' => 4, 'name' => 'Pakan Anjing'],
            ['id' => 5, 'name' => 'Pakan Hamster'],
            ['id' => 6, 'name' => 'Aksesoris Akuarium'],
            ['id' => 7, 'name' => 'Aksesoris Kandang'],
            ['id' => 8, 'name' => 'Obat & Vitamin'],
            ['id' => 9, 'name' => 'Perawatan Hewan'],
            ['id' => 10, 'name' => 'Lainnya'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['id' => $category['id']],
                [
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
