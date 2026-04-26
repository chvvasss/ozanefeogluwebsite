<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            SettingSeeder::class,
            PublicationSeeder::class,
            WritingSeeder::class,
            PageSeeder::class,
            LegalPageSeeder::class,
        ]);
    }
}
