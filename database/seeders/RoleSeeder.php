<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super-admin', 'admin', 'editor', 'contributor', 'viewer'];

        foreach ($roles as $name) {
            Role::findOrCreate($name, 'web');
        }
    }
}
