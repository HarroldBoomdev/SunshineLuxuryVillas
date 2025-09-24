<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    public function run()
    {
        Section::updateOrCreate([
            'slug' => 'home-video'
        ], [
            'data' => json_encode([
                'headline' => 'Five Star',
                'subheadline' => 'LUXURY VILLAS in Cyprus',
                'button_text' => 'View Listings',
                'button_link' => '/properties?category=luxury-villas',
                'video_thumbnail' => '/images/luxury-villa-thumbnail.jpg',
                'video_url' => 'https://www.youtube.com/embed/example-video'
            ])
        ]);

    }
}
