<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\SuspiciousLoginAttempts;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    RateLimiter::clear('login');
    RateLimiter::clear('password-reset');
});

/* ─────────────────────────────────────────────────────────────────────────
 *  FIX 1 — Public registration is closed
 * ────────────────────────────────────────────────────────────────────── */

test('public registration route is not accessible (feature disabled)', function () {
    // The /register route should NOT be registered when the feature is off.
    $response = $this->get('/register');

    // Either 404 (route not registered) or 500 (CreateNewUser action throws).
    // Both shape "registration is unavailable to public visitors".
    expect($response->status())->toBeIn([404, 500, 302]);
});

/* ─────────────────────────────────────────────────────────────────────────
 *  FIX 2 — Account lockout is enforced before password check
 * ────────────────────────────────────────────────────────────────────── */

test('locked account cannot log in even with correct password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-horse-battery-staple-2026'),
        'locked_until' => now()->addMinutes(15),
        'failed_attempts' => 10,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-horse-battery-staple-2026',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('expired lockout (locked_until in the past) allows login again', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-horse-battery-staple-2026'),
        'locked_until' => now()->subMinutes(5),
        'email_verified_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-horse-battery-staple-2026',
    ]);

    expect($response->status())->toBeIn([200, 302]);
    $this->assertAuthenticatedAs($user->fresh());
});

/* ─────────────────────────────────────────────────────────────────────────
 *  FIX 4 — Forgot password is rate-limited (3/hour by ip+email)
 * ────────────────────────────────────────────────────────────────────── */

test('forgot password endpoint is rate-limited after 3 attempts', function () {
    $email = 'rate-limit-test@example.com';

    for ($i = 1; $i <= 3; $i++) {
        $this->post('/forgot-password', ['email' => $email])
            ->assertStatus(302);
    }

    // 4th attempt should be throttled with a back-redirect + email error.
    $response = $this->post('/forgot-password', ['email' => $email]);
    $response->assertSessionHasErrors('email');
});

/* ─────────────────────────────────────────────────────────────────────────
 *  FIX 6 — Suspicious login attempt notification
 * ────────────────────────────────────────────────────────────────────── */

test('user is notified after 5 consecutive failed login attempts', function () {
    Notification::fake();

    $user = User::factory()->create([
        'password' => Hash::make('right-password'),
        'failed_attempts' => 0,
    ]);

    // 5 wrong attempts → notification on the 5th
    for ($i = 1; $i <= 5; $i++) {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password-'.$i,
        ]);
    }

    Notification::assertSentTo($user->fresh(), SuspiciousLoginAttempts::class, function ($notif) {
        return $notif->attemptCount === 5 && $notif->accountLocked === false;
    });
});

test('user receives lockout notification when threshold reached', function () {
    Notification::fake();
    config(['security.login.daily_lockout_threshold' => 6]); // small window for test
    config(['security.login.alert_threshold' => 999]);       // disable alert mail; only lockout

    $user = User::factory()->create([
        'password' => Hash::make('right-password'),
        'failed_attempts' => 0,
    ]);

    for ($i = 1; $i <= 6; $i++) {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-'.$i,
        ]);
    }

    expect($user->fresh()->locked_until)->not->toBeNull();

    Notification::assertSentTo($user->fresh(), SuspiciousLoginAttempts::class, function ($notif) {
        return $notif->accountLocked === true;
    });
});
