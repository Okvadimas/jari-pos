<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaigns = [
            [
                'title' => 'Welcome to Jari POS',
                'description' => 'Rasakan masa depan manajemen POS dengan pelacakan dan analitik yang komprehensif.',
                'image' => 'images/slides/slide-a.jpg',
                'type' => 'slider',
                'is_published' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Manage Your Business',
                'description' => 'Kelola bisnis Anda dengan mudah dan efisien.',
                'image' => 'images/slides/slide-b.jpg', 
                'type' => 'slider',
                'is_published' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Track Your Orders',
                'description' => 'Lacak pesanan Anda dengan mudah dan cepat.',
                'image' => 'images/slides/slide-c.jpg', 
                'type' => 'facebook',
                'is_published' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Draft Slide',
                'description' => 'Slide ini masih draft.',
                'image' => 'images/slides/slide-c.jpg', 
                'type' => 'slider', 
                'is_published' => 0, // 0 = draft
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::updateOrCreate(
                ['title' => $campaign['title']],
                $campaign
            );
        }
    }
}
