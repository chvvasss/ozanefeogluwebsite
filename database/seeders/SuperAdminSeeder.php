<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@ozanefeoglu.com');
        $name = (string) env('ADMIN_NAME', 'Yönetici');
        $password = (string) env('ADMIN_PASSWORD', 'change-this-on-first-login');

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'locale' => 'tr',
                'password_changed_at' => now(),
            ]
        );

        $user->syncRoles(['super-admin']);
    }
}
