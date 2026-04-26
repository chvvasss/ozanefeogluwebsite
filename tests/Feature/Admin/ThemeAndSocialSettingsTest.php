<?php

declare(strict_types=1);

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

it('renders the social admin tab', function () {
    $u = User::factory()->create();
    $u->assignRole('admin');

    $this->actingAs($u)->get('/admin/settings/social')
        ->assertOk()
        ->assertSee('Mastodon')
        ->assertSee('Bluesky')
        ->assertSee('GitHub');
});

it('persists social urls through the admin form', function () {
    $u = User::factory()->create();
    $u->assignRole('admin');

    $this->actingAs($u)->put('/admin/settings/social', [
        'social' => [
            'mastodon_url' => 'https://mastodon.social/@ozan',
            'bluesky_url' => 'https://bsky.app/profile/ozan.test',
            'x_url' => '',
            'instagram_url' => '',
            'linkedin_url' => '',
            'github_url' => '',
        ],
    ])->assertRedirect('/admin/settings/social');

    expect(site_setting('social.mastodon_url'))->toBe('https://mastodon.social/@ozan');
    expect(site_setting('social.bluesky_url'))->toBe('https://bsky.app/profile/ozan.test');
    expect(site_setting('social.x_url'))->toBeNull();
});

it('rejects invalid social urls', function () {
    $u = User::factory()->create();
    $u->assignRole('admin');

    $this->actingAs($u)->put('/admin/settings/social', [
        'social' => [
            'mastodon_url' => 'not-a-url',
        ],
    ])->assertSessionHasErrors('social.mastodon_url');
});

it('renders the theme admin tab with feature toggles', function () {
    $u = User::factory()->create();
    $u->assignRole('admin');

    $this->actingAs($u)->get('/admin/settings/theme')
        ->assertOk()
        ->assertSee('Dispatch')
        ->assertSee('Karanlık mod')
        ->assertSee('RSS yayını')
        ->assertSee('Bülten');
});

it('persists theme + features through the admin form', function () {
    $u = User::factory()->create();
    $u->assignRole('admin');

    $this->actingAs($u)->put('/admin/settings/theme', [
        'theme' => ['preset' => 'dispatch', 'dark_mode' => 'dark'],
        'features' => [
            'feed_enabled' => '1',
            'newsletter_enabled' => '0',
            'search_enabled' => '0',
            'demo_content_banner' => '1',
        ],
    ])->assertRedirect('/admin/settings/theme');

    expect(site_setting('theme.dark_mode'))->toBe('dark');
    expect(site_setting('features.feed_enabled'))->toBeTrue();
    expect(site_setting('features.newsletter_enabled'))->toBeFalse();
});

it('rejects an unsupported dark_mode value', function () {
    $u = User::factory()->create();
    $u->assignRole('admin');

    $this->actingAs($u)->put('/admin/settings/theme', [
        'theme' => ['preset' => 'dispatch', 'dark_mode' => 'neon'],
        'features' => [
            'feed_enabled' => '0',
            'newsletter_enabled' => '0',
            'search_enabled' => '0',
            'demo_content_banner' => '0',
        ],
    ])->assertSessionHasErrors('theme.dark_mode');
});

it('renders social links in the footer only when set', function () {
    // None set → no social links block
    SettingsRepository::set('social.mastodon_url', null, 'social');
    SettingsRepository::set('social.bluesky_url', null, 'social');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('Açık hesaplar');

    // Set one → block appears
    SettingsRepository::set('social.mastodon_url', 'https://mastodon.social/@o', 'social');
    $this->get('/')
        ->assertOk()
        ->assertSee('Açık hesaplar')
        ->assertSee('mastodon.social/@o', false);
});

it('includes the KVKK link in the footer by default', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('/kvkk', false)
        ->assertSee('KVKK');
});
