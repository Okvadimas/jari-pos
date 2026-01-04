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
        // Clear existing data
        Campaign::truncate();

        $campaigns = [
            [
                'title' => 'Welcome to Jari POS',
                'description' => 'Rasakan masa depan manajemen POS dengan pelacakan dan analitik yang komprehensif.',
                'image' => 'images/slides/slide-a.jpg', // Ensure this image exists or use a placeholder
                'type' => 'slider',
                'status' => 'active',
            ],
            [
                'title' => 'Manage Your Business',
                'description' => 'Kelola bisnis Anda dengan mudah dan efisien.',
                'image' => 'images/slides/slide-b.jpg', 
                'type' => 'slider',
                'status' => 'active',
            ],
            [
                'title' => 'Track Your Orders',
                'description' => 'Lacak pesanan Anda dengan mudah dan cepat.',
                'image' => 'images/slides/slide-c.jpg', 
                'type' => 'facebook', // Test filtering
                'status' => 'active',
            ],
             [
                'title' => 'Inactive Slide',
                'description' => 'Slide ini tidak aktif.',
                'image' => 'images/slides/slide-c.jpg', 
                'type' => 'slider', 
                'status' => 'inactive', // Test status filtering
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::create($campaign);
        }
    }
}
