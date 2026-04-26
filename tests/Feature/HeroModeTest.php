<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Writing;
use App\Support\SettingsRepository;
use Database\Seeders\SettingSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    SettingsRepository::flush();
    $this->seed(SettingSeeder::class);
});

it('renders typographic hero when no hero-eligible writing exists', function () {
    SettingsRepository::set('hero.mode', 'featured_photo', 'hero');

    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('scene--typographic', false);
    $response->assertDontSee('scene--featured-photo', false);
});

it('falls back to typographic when featured_photo mode finds no cover', function () {
    // Make one hero_eligible published writing without a cover
    Writing::query()->create([
        'slug' => 'demo-no-cover',
        'kind' => 'editoryal',
        'title' => ['tr' => 'Kapak yok'],
        'excerpt' => ['tr' => 'x'],
        'body' => ['tr' => '<p>body</p>'],
        'is_published' => true,
        'published_at' => now()->subDay(),
        'hero_eligible' => true,
    ]);

    SettingsRepository::set('hero.mode', 'featured_photo', 'hero');

    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('scene--typographic', false);
});

it('uses the portrait hero when mode=portrait and portrait url is set', function () {
    SettingsRepository::set('hero.mode', 'portrait', 'hero');
    SettingsRepository::set('identity.portrait_url', '/images/fake-portrait.jpg', 'identity');
    SettingsRepository::set('identity.portrait_credit', 'Foto: Test', 'identity');

    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('scene--portrait', false);
    $response->assertSee('/images/fake-portrait.jpg', false);
});

it('honors hero.mode=typographic regardless of eligible writings', function () {
    Writing::query()->create([
        'slug' => 'demo-typo',
        'kind' => 'editoryal',
        'title' => ['tr' => 'X'],
        'body' => ['tr' => '<p>x</p>'],
        'is_published' => true,
        'published_at' => now()->subDay(),
        'hero_eligible' => true,
    ]);

    SettingsRepository::set('hero.mode', 'typographic', 'hero');

    $this->get('/')
        ->assertOk()
        ->assertSee('scene--typographic', false)
        ->assertDontSee('scene--featured-photo', false);
});

it('renders the admin hero tab with mode options and candidate picker', function () {
    $user = User::factory()->create();
    Role::findOrCreate('admin', 'web');
    $user->assignRole('admin');
    config(['security.require_2fa_for_admin' => false]);

    $response = $this->actingAs($user)->get('/admin/settings/hero');
    $response->assertOk();
    $response->assertSee('Hero modu');
    $response->assertSee('Öne çıkan fotoğraf');
    $response->assertSee('Tipografik');
});

it('persists hero mode change through the admin settings form', function () {
    $user = User::factory()->create();
    Role::findOrCreate('admin', 'web');
    $user->assignRole('admin');
    config(['security.require_2fa_for_admin' => false]);

    $this->actingAs($user)->put('/admin/settings/hero', [
        'hero' => [
            'mode' => 'typographic',
            'featured_writing_id' => '',
        ],
    ])->assertRedirect('/admin/settings/hero');

    expect(site_setting('hero.mode'))->toBe('typographic');
    expect(site_setting('hero.featured_writing_id'))->toBeNull();
});

it('rejects an invalid hero mode value', function () {
    $user = User::factory()->create();
    Role::findOrCreate('admin', 'web');
    $user->assignRole('admin');
    config(['security.require_2fa_for_admin' => false]);

    $this->actingAs($user)->put('/admin/settings/hero', [
        'hero' => ['mode' => 'galactic', 'featured_writing_id' => ''],
    ])->assertSessionHasErrors('hero.mode');
});
