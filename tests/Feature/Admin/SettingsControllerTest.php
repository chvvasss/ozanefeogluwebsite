<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Models\User;
use App\Support\SettingsRepository;
use Database\Seeders\SettingSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    config(['security.require_2fa_for_admin' => false]);
    SettingsRepository::flush();
    $this->seed(SettingSeeder::class);
});

it('redirects /admin/settings to identity group by default', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get('/admin/settings')
        ->assertRedirect('/admin/settings/identity');
});

it('shows the identity form populated with current values', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/settings/identity');

    $response->assertOk();
    $response->assertSee('Kimlik');
    $response->assertSee('Ozan Efeoğlu');
    $response->assertSee('Foto muhabir');
});

it('redirects an unknown group back to the settings index', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get('/admin/settings/ghost')
        ->assertRedirect('/admin/settings');
});

it('persists identity form submission and busts the cache', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $payload = [
        'identity' => [
            'name' => 'Ozan E. Test',
            'role_primary' => 'Editör',
            'role_secondary' => 'foto muhabir',
            'role_tertiary' => '',
            'base' => 'İstanbul',
            'affiliation' => 'AA',
            'affiliation_approved' => '1',
            'description' => 'test',
            'manifesto_quote' => 'x',
            'current_context' => 'y',
        ],
    ];

    $response = $this->actingAs($user)
        ->put('/admin/settings/identity', $payload);

    $response->assertRedirect('/admin/settings/identity');
    $response->assertSessionHas('status');

    // DB + SettingsRepository cache both reflect new value
    expect(Setting::query()->where('key', 'identity.name')->value('value'))
        ->toBe('Ozan E. Test');
    expect(site_setting('identity.name'))->toBe('Ozan E. Test');
    expect(site_setting('identity.affiliation_approved'))->toBeTrue();
});

it('rejects invalid contact email and retention', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->put('/admin/settings/contact', [
        'contact' => [
            'email' => 'not-an-email',
            'retention_days' => 9999,
        ],
    ]);

    $response->assertSessionHasErrors(['contact.email', 'contact.retention_days']);
});

it('persists nav toggle and hero cta labels', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)->put('/admin/settings/nav', [
        'nav' => ['show_visuals' => '0'],
        'hero' => [
            'eyebrow' => 'Haber Masası',
            'cta_primary_label' => 'Yazılar',
            'cta_primary_url' => '/yazilar',
            'cta_secondary_label' => '',
            'cta_secondary_url' => '',
        ],
    ])->assertRedirect('/admin/settings/nav');

    expect(site_setting('nav.show_visuals'))->toBeFalse();
    expect(site_setting('hero.eyebrow'))->toBe('Haber Masası');
    expect(site_setting('hero.cta_secondary_label'))->toBeNull();
});

it('denies anonymous access to the settings surface', function () {
    $this->get('/admin/settings')->assertRedirect();
    $this->get('/admin/settings/identity')->assertRedirect();
    $this->put('/admin/settings/identity', [])->assertRedirect();
});
