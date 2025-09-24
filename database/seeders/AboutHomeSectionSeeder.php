<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class AboutHomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Section::updateOrCreate(
            ['slug' => 'about-home'],
            [
                'title' => 'SLV Estates, Leading Cyprus Estate Agents',
                'content' => json_encode([
                    'about_title' => 'About SLV Estates Cyprus',
                    'about_body' => 'At SLV Estates, we don’t just sell properties: we craft dreams, create lifestyles, and turn visions into reality. With an extensive island-wide reach, we are dedicated partners in finding your perfect home amidst the beauty of Cyprus.',
                    'why_title' => 'Why Choose SLV Estates Cyprus?',
                    'why_body' => 'With over 25 years of experience in buying, selling, and building properties, we offer unparalleled service. Our dedicated team of multilingual professionals operates from offices across the country and guarantees A+ service for our clients.'
                ])
            ]
        );
    }
}
