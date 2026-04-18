<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Features;
use PragmaRX\Google2FA\Google2FA;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
    config(['security.require_2fa_for_admin' => false]);
});

it('displays the enrollment page for an authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/two-factor');
    $response->assertOk();
    $response->assertSee('Kurulumu başlat');
});

it('enables two-factor authentication and stores a secret', function () {
    if (! Features::enabled(Features::twoFactorAuthentication())) {
        $this->markTestSkipped('2FA feature not enabled.');
    }

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin/two-factor/enable');
    $response->assertRedirect();

    $user->refresh();
    expect($user->two_factor_secret)->not->toBeNull();
    expect($user->two_factor_recovery_codes)->not->toBeNull();
    expect($user->hasTwoFactorEnabled())->toBeFalse(); // not confirmed yet
});

it('confirms two-factor with a valid TOTP code', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post('/admin/two-factor/enable');
    $user->refresh();

    $secret = decrypt($user->two_factor_secret);
    $code = (new Google2FA)->getCurrentOtp($secret);

    $response = $this->actingAs($user)->post('/admin/two-factor/confirm', ['code' => $code]);
    $response->assertRedirect();

    $user->refresh();
    expect($user->hasTwoFactorEnabled())->toBeTrue();
});

it('rejects an invalid TOTP code during confirmation', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post('/admin/two-factor/enable');

    $response = $this->actingAs($user)
        ->from('/admin/two-factor')
        ->post('/admin/two-factor/confirm', ['code' => '000000']);

    $response->assertSessionHasErrors();
    $user->refresh();
    expect($user->hasTwoFactorEnabled())->toBeFalse();
});

it('requires current password to disable two-factor', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-horse-battery-staple')]);
    $this->actingAs($user)->post('/admin/two-factor/enable');
    $user->refresh();
    $secret = decrypt($user->two_factor_secret);
    $code = (new Google2FA)->getCurrentOtp($secret);
    $this->actingAs($user)->post('/admin/two-factor/confirm', ['code' => $code]);

    $response = $this->actingAs($user)->post('/admin/two-factor/disable', [
        'current_password' => 'wrong-password',
    ]);
    $response->assertSessionHasErrors('current_password');

    $response = $this->actingAs($user)->post('/admin/two-factor/disable', [
        'current_password' => 'correct-horse-battery-staple',
    ]);
    $response->assertRedirect();

    $user->refresh();
    expect($user->two_factor_secret)->toBeNull();
});
