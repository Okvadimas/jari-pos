<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            // Pakan Ikan (category_id: 1)
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Pelet Ikan Koi Hi-Growth',
                'description' => 'Pelet premium untuk pertumbuhan ikan koi dengan protein tinggi',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Pelet Ikan Lele Super',
                'description' => 'Pakan ikan lele dengan nutrisi lengkap untuk pertumbuhan cepat',
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'name' => 'Pelet Ikan Hias Tropical',
                'description' => 'Pakan ikan hias tropis dengan warna cerah',
            ],

            // Pakan Burung (category_id: 2)
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'Voer Burung Murai',
                'description' => 'Voer khusus burung murai batu dengan kandungan protein tinggi',
            ],
            [
                'id' => 5,
                'category_id' => 2,
                'name' => 'Biji Milet Putih',
                'description' => 'Biji milet putih berkualitas untuk burung kenari dan lovebird',
            ],
            [
                'id' => 6,
                'category_id' => 2,
                'name' => 'Kroto Segar',
                'description' => 'Kroto segar untuk burung kicau',
            ],

            // Pakan Kucing (category_id: 3)
            [
                'id' => 7,
                'category_id' => 3,
                'name' => 'Cat Food Premium Adult',
                'description' => 'Makanan kucing dewasa dengan formula premium',
            ],
            [
                'id' => 8,
                'category_id' => 3,
                'name' => 'Cat Food Kitten',
                'description' => 'Makanan khusus anak kucing dengan nutrisi pertumbuhan',
            ],
            [
                'id' => 9,
                'category_id' => 3,
                'name' => 'Wet Food Tuna',
                'description' => 'Makanan basah kucing rasa tuna',
            ],

            // Pakan Anjing (category_id: 4)
            [
                'id' => 10,
                'category_id' => 4,
                'name' => 'Dog Food Adult Large Breed',
                'description' => 'Makanan anjing dewasa ras besar',
            ],
            [
                'id' => 11,
                'category_id' => 4,
                'name' => 'Dog Food Puppy',
                'description' => 'Makanan khusus anak anjing',
            ],

            // Aksesoris Akuarium (category_id: 6)
            [
                'id' => 12,
                'category_id' => 6,
                'name' => 'Filter Akuarium Mini',
                'description' => 'Filter akuarium untuk ukuran 20-40 liter',
            ],
            [
                'id' => 13,
                'category_id' => 6,
                'name' => 'Lampu LED Akuarium',
                'description' => 'Lampu LED hemat energi untuk akuarium',
            ],

            // Obat & Vitamin (category_id: 8)
            [
                'id' => 14,
                'category_id' => 8,
                'name' => 'Vitamin Burung Multivit',
                'description' => 'Vitamin lengkap untuk meningkatkan stamina burung',
            ],
            [
                'id' => 15,
                'category_id' => 8,
                'name' => 'Obat Anti Kutu Kucing',
                'description' => 'Obat tetes anti kutu untuk kucing',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['id' => $product['id']],
                [
                    'category_id' => $product['category_id'],
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
