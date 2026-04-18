<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
    config(['fortify.features.registration' => true]);
});

afterEach(function (): void {
    config(['fortify.features.registration' => false]);
});

it('rejects passwords shorter than the configured minimum', function () {
    $action = app(CreateNewUser::class);

    expect(fn () => $action->create([
        'name' => 'Ozan',
        'email' => 'ozan+short@example.com',
        'password' => 'too-short',
        'password_confirmation' => 'too-short',
    ]))->toThrow(ValidationException::class);
});

it('accepts strong new-user passwords when HIBP is disabled', function () {
    config(['security.hibp.enabled' => false]);
    $action = app(CreateNewUser::class);

    $user = $action->create([
        'name' => 'Ozan',
        'email' => 'ozan+ok@example.com',
        'password' => 'correct-horse-battery-staple-42',
        'password_confirmation' => 'correct-horse-battery-staple-42',
    ]);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->password_changed_at)->not->toBeNull();
});

it('rejects a pwned password via HIBP k-anonymity', function () {
    config(['security.hibp.enabled' => true]);
    // Stub Http to simulate "compromised" response for any prefix
    $sha1 = strtoupper(sha1('correct-horse-battery-staple-42'));
    $suffix = substr($sha1, 5);
    Http::fake([
        '*' => Http::response("{$suffix}:9999\nABCDEF0123:1", 200),
    ]);

    $action = app(CreateNewUser::class);

    expect(fn () => $action->create([
        'name' => 'Ozan',
        'email' => 'ozan+pwned@example.com',
        'password' => 'correct-horse-battery-staple-42',
        'password_confirmation' => 'correct-horse-battery-staple-42',
    ]))->toThrow(ValidationException::class);
});

it('blocks registration entirely when the feature is disabled', function () {
    config(['fortify.features.registration' => false]);
    $action = app(CreateNewUser::class);

    expect(fn () => $action->create([
        'name' => 'Ozan',
        'email' => 'ozan+blocked@example.com',
        'password' => 'correct-horse-battery-staple-42',
        'password_confirmation' => 'correct-horse-battery-staple-42',
    ]))->toThrow(ValidationException::class);
});
