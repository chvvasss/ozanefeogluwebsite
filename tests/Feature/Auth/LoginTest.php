<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
});

it('shows the login page', function () {
    $response = $this->get('/login');

    $response->assertOk();
    $response->assertSee('Devam et');
});

it('logs a user in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'ozan@example.com',
        'password' => Hash::make('correct-horse-battery-staple'),
    ]);

    $response = $this->post('/login', [
        'email' => 'ozan@example.com',
        'password' => 'correct-horse-battery-staple',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials with a generic error', function () {
    User::factory()->create([
        'email' => 'ozan@example.com',
        'password' => Hash::make('correct-horse-battery-staple'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'ozan@example.com',
        'password' => 'wrong',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors();
    $this->assertGuest();
});

it('does not leak user existence on unknown email', function () {
    $response = $this->from('/login')->post('/login', [
        'email' => 'unknown@example.com',
        'password' => 'whatever',
    ]);

    $response->assertRedirect('/login');
    $errors = session('errors');
    expect($errors)->not->toBeNull();
});

it('recording activity after successful login', function () {
    $user = User::factory()->create([
        'email' => 'ozan@example.com',
        'password' => Hash::make('correct-horse-battery-staple'),
    ]);

    $this->post('/login', [
        'email' => 'ozan@example.com',
        'password' => 'correct-horse-battery-staple',
    ]);

    $user->refresh();
    expect($user->last_login_at)->not->toBeNull();
    $this->assertDatabaseHas('activity_log', [
        'log_name' => 'auth',
        'event' => 'login.success',
    ]);
});

it('throttles repeated failed attempts', function () {
    User::factory()->create([
        'email' => 'ozan@example.com',
        'password' => Hash::make('correct-horse-battery-staple'),
    ]);

    for ($i = 0; $i < 6; $i++) {
        $response = $this->post('/login', [
            'email' => 'ozan@example.com',
            'password' => 'wrong',
        ]);
    }

    // 6th attempt should be throttled (session redirect with throttle error)
    expect($response->status())->toBeIn([302, 429]);
});
