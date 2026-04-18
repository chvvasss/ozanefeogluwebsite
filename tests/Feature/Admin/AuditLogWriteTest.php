<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
});

it('writes a login.success event with causer and ip', function () {
    $user = User::factory()->create([
        'email' => 'ozan@example.com',
        'password' => Hash::make('correct-horse-battery-staple'),
    ]);

    $this->post('/login', [
        'email' => 'ozan@example.com',
        'password' => 'correct-horse-battery-staple',
    ]);

    $log = Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'login.success')
        ->latest()
        ->first();

    expect($log)->not->toBeNull();
    expect($log->causer_id)->toBe($user->id);
    expect($log->properties->get('ip'))->not->toBeEmpty();
});

it('writes a login.failed event on bad credentials', function () {
    User::factory()->create([
        'email' => 'ozan@example.com',
        'password' => Hash::make('correct-horse-battery-staple'),
    ]);

    $this->post('/login', [
        'email' => 'ozan@example.com',
        'password' => 'wrong',
    ]);

    $log = Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'login.failed')
        ->latest()
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties->get('reason'))->toBe('bad_credentials');
    expect($log->properties->get('email_attempted'))->toBe('ozan@example.com');
});

it('denies audit-log access to non-privileged roles', function () {
    config(['security.require_2fa_for_admin' => false]);
    Role::findOrCreate('viewer', 'web');

    $user = User::factory()->create();
    $user->assignRole('viewer');

    $response = $this->actingAs($user)->get('/admin/audit-log');
    $response->assertForbidden();
});

it('allows audit-log access to super-admin', function () {
    config(['security.require_2fa_for_admin' => false]);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->actingAs($user)->get('/admin/audit-log');
    $response->assertOk();
});
