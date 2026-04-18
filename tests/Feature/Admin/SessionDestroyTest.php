<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserDevice;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    config(['security.require_2fa_for_admin' => false]);
});

it('lists the current user devices', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    UserDevice::query()->create([
        'user_id' => $user->id,
        'session_id' => 'other-session-id',
        'ip_address' => '1.1.1.1',
        'user_agent' => 'curl/8.0',
        'device_label' => 'Chrome · macOS',
        'last_active_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/admin/profile/sessions');
    $response->assertOk();
    $response->assertSee('Chrome · macOS');
});

it('destroys a non-current device', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $device = UserDevice::query()->create([
        'user_id' => $user->id,
        'session_id' => 'another-id',
        'ip_address' => '2.2.2.2',
        'user_agent' => 'Firefox',
        'device_label' => 'Firefox · Linux',
        'last_active_at' => now(),
    ]);

    $response = $this->actingAs($user)->delete("/admin/profile/sessions/{$device->id}");
    $response->assertRedirect();

    expect(UserDevice::query()->where('id', $device->id)->exists())->toBeFalse();
});

it('forbids destroying another user\'s device', function () {
    $me = User::factory()->create();
    $me->assignRole('admin');
    $other = User::factory()->create();

    $otherDevice = UserDevice::query()->create([
        'user_id' => $other->id,
        'session_id' => 'other-session',
        'ip_address' => '3.3.3.3',
        'user_agent' => 'UA',
        'device_label' => 'Someone else',
        'last_active_at' => now(),
    ]);

    $response = $this->actingAs($me)->delete("/admin/profile/sessions/{$otherDevice->id}");
    $response->assertForbidden();
    expect(UserDevice::query()->where('id', $otherDevice->id)->exists())->toBeTrue();
});
