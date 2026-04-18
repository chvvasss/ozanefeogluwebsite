<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site.title',      'value' => 'Ozan Efeoğlu',                                          'group' => 'identity', 'is_public' => true],
            ['key' => 'site.tagline',    'value' => 'Dispatches from places the news cycle forgets.',        'group' => 'identity', 'is_public' => true],
            ['key' => 'site.description', 'value' => 'Ozan Efeoğlu — saha muhabiri. Reports, column, photography.', 'group' => 'identity', 'is_public' => true],
            ['key' => 'theme.preset',    'value' => 'dispatch',                                              'group' => 'theme',    'is_public' => true],
            ['key' => 'theme.dark_mode', 'value' => 'system',                                                'group' => 'theme',    'is_public' => true],
            ['key' => 'contact.email',   'value' => 'press@ozanefeoglu.com',                                 'group' => 'contact',  'is_public' => true],
            ['key' => 'contact.signal',  'value' => null,                                                    'group' => 'contact',  'is_public' => true],
            ['key' => 'current.assignment.label',    'value' => 'currently · on assignment',                 'group' => 'status',   'is_public' => true],
            ['key' => 'current.assignment.location', 'value' => 'undisclosed · eastern theatre',             'group' => 'status',   'is_public' => true],
            ['key' => 'current.assignment.since',    'value' => 'since 2026-03',                             'group' => 'status',   'is_public' => true],
        ];

        foreach ($defaults as $row) {
            Setting::query()->updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
