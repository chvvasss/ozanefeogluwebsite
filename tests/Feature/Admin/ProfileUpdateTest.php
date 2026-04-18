<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    config(['security.require_2fa_for_admin' => false]);
});

it('shows the profile page', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/profile');
    $response->assertOk();
    $response->assertSee($user->email);
});

it('updates the profile with valid data', function () {
    $user = User::factory()->create(['locale' => 'tr']);
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post('/admin/profile', [
        'name' => 'Ozan E.',
        'email' => 'new@example.com',
        'locale' => 'en',
    ]);

    $response->assertSessionHasNoErrors();
    $user->refresh();
    expect($user->name)->toBe('Ozan E.')
        ->and($user->email)->toBe('new@example.com')
        ->and($user->locale)->toBe('en');
});

it('rejects a duplicate email belonging to another user', function () {
    $other = User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post('/admin/profile', [
        'name' => 'Whoever',
        'email' => 'taken@example.com',
        'locale' => 'tr',
    ]);

    $response->assertSessionHasErrors('email');
});

it('rejects an unsupported locale', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post('/admin/profile', [
        'name' => 'Ozan',
        'email' => $user->email,
        'locale' => 'de',
    ]);

    $response->assertSessionHasErrors('locale');
});
