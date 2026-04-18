<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
});

it('regenerates the session id after a successful login', function () {
    $user = User::factory()->create([
        'email' => 'ozan@example.com',
        'password' => Hash::make('correct-horse-battery-staple'),
    ]);

    // Start a guest session
    $this->startSession();
    $before = session()->getId();

    $this->post('/login', [
        'email' => 'ozan@example.com',
        'password' => 'correct-horse-battery-staple',
    ]);

    $after = session()->getId();

    expect($after)->not->toBe($before);
});

it('invalidates the session on logout', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->startSession();
    $idBefore = session()->getId();
    session()->put('marker', 'present');

    $response = $this->post('/admin/logout');
    $response->assertRedirect();

    expect(session()->has('marker'))->toBeFalse();
    expect($this->isAuthenticated())->toBeFalse();
});
