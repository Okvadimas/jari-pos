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
                'image' => 'images/slides/slide-a.jpg', // Ensure this image exists or use a placeholder
                'type' => 'slider',
            ],
            [
                'title' => 'Manage Your Business',
                'description' => 'Kelola bisnis Anda dengan mudah dan efisien.',
                'image' => 'images/slides/slide-b.jpg', 
                'type' => 'slider',
            ],
            [
                'title' => 'Track Your Orders',
                'description' => 'Lacak pesanan Anda dengan mudah dan cepat.',
                'image' => 'images/slides/slide-c.jpg', 
                'type' => 'facebook', // Test filtering
            ],
             [
                'title' => 'Inactive Slide',
                'description' => 'Slide ini tidak aktif.',
                'image' => 'images/slides/slide-c.jpg', 
                'type' => 'slider', 
                'status' => 0, // 0 = inactive
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
