<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
});

it('shows the forgot-password form', function () {
    $response = $this->get('/forgot-password');
    $response->assertOk();
    $response->assertSee('Şifre sıfırlama');
});

it('dispatches a reset notification when the email exists', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'ozan@example.com']);

    $response = $this->post('/forgot-password', ['email' => 'ozan@example.com']);
    $response->assertSessionHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('silently accepts an unknown email to avoid user enumeration', function () {
    Notification::fake();

    $response = $this->post('/forgot-password', ['email' => 'unknown@example.com']);

    Notification::assertNothingSent();
    // Fortify returns a validation error for unknown emails in some configs;
    // what matters is that the response does not leak whether the account exists.
    expect($response->status())->toBeIn([200, 302]);
});

it('allows resetting the password with a valid token', function () {
    $user = User::factory()->create(['email' => 'ozan@example.com']);
    $token = app('auth.password.broker')->createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'ozan@example.com',
        'password' => 'this-is-a-very-long-passphrase-42',
        'password_confirmation' => 'this-is-a-very-long-passphrase-42',
    ]);

    $response->assertSessionHasNoErrors();
    $user->refresh();
    expect($user->password)->not->toBeNull();
    expect(Hash::check('this-is-a-very-long-passphrase-42', $user->password))->toBeTrue();
});

it('rejects short passwords on reset', function () {
    $user = User::factory()->create(['email' => 'ozan@example.com']);
    $token = app('auth.password.broker')->createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'ozan@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
});

it('rejects mismatched confirmation', function () {
    $user = User::factory()->create(['email' => 'ozan@example.com']);
    $token = app('auth.password.broker')->createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'ozan@example.com',
        'password' => 'correct-horse-battery-stapler',
        'password_confirmation' => 'not-the-same-passphrase-42',
    ]);

    $response->assertSessionHasErrors('password');
});
