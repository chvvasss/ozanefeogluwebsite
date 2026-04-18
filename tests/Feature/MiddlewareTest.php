<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('super-admin', 'web');
});

it('allows admin onto the dashboard when 2FA gate is off', function () {
    config(['security.require_2fa_for_admin' => false]);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin');
    $response->assertOk();
});

it('forces 2FA enrollment when required and not yet enabled', function () {
    config(['security.require_2fa_for_admin' => true]);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin');
    $response->assertRedirect(route('admin.two-factor.setup'));
});

it('does not redirect when on an allowlisted 2FA route', function () {
    config(['security.require_2fa_for_admin' => true]);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/two-factor');
    $response->assertOk();
});

it('redirects guests to the login page', function () {
    $response = $this->get('/admin/profile');
    $response->assertRedirect('/admin/login');
});

it('sets the application locale from the user record', function () {
    $user = User::factory()->create(['locale' => 'en']);
    $user->assignRole('admin');

    config(['security.require_2fa_for_admin' => false]);

    $this->actingAs($user)->get('/admin');
    expect(app()->getLocale())->toBe('en');
});
