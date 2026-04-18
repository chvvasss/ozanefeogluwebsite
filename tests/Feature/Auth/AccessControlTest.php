<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('viewer', 'web');
});

it('redirects guests from admin to login', function () {
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/login');
});

it('allows authenticated admin users onto the dashboard', function () {
    config(['security.require_2fa_for_admin' => false]);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin');

    $response->assertOk();
});

it('blocks viewer role from audit log', function () {
    config(['security.require_2fa_for_admin' => false]);

    $user = User::factory()->create();
    $user->assignRole('viewer');

    $response = $this->actingAs($user)->get('/admin/audit-log');

    $response->assertForbidden();
});

it('allows admin role onto audit log', function () {
    config(['security.require_2fa_for_admin' => false]);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/audit-log');

    $response->assertOk();
});

it('requires 2FA enrollment when admin gate enabled', function () {
    config(['security.require_2fa_for_admin' => true]);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin');

    $response->assertRedirect(route('admin.two-factor.setup'));
});
