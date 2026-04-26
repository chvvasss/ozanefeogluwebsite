<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Publication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'The New York Times', 'url' => 'https://www.nytimes.com'],
            ['name' => 'Le Monde',           'url' => 'https://www.lemonde.fr'],
            ['name' => 'Reuters',            'url' => 'https://www.reuters.com'],
            ['name' => 'The Guardian',       'url' => 'https://www.theguardian.com'],
            ['name' => 'Foreign Policy',     'url' => 'https://foreignpolicy.com'],
            ['name' => 'TRT World',          'url' => 'https://www.trtworld.com'],
            ['name' => 'Anadolu',            'url' => 'https://www.aa.com.tr'],
            ['name' => 'BBC Türkçe',         'url' => 'https://www.bbc.com/turkce'],
        ];

        foreach (array_values($rows) as $index => $row) {
            Publication::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'slug' => Str::slug($row['name']),
                    'url' => $row['url'],
                    'sort_order' => $index,
                ]
            );
        }
    }
}
