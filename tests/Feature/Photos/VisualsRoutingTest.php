<?php

declare(strict_types=1);

use App\Models\Photo;
use App\Support\SettingsRepository;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    SettingsRepository::flush();
    $this->seed(SettingSeeder::class);
    Storage::fake('public');
});

/**
 * Helper: create a photo with a real persistent image attached so the
 * index (which filters whereHas media) can see it. We write the image
 * to a temp file ourselves (instead of Laravel's File::image which uses
 * short-lived tmp paths that MediaLibrary sometimes cannot re-open).
 */
function photoWithImage(array $overrides = []): Photo
{
    $photo = Photo::factory()->published()->create($overrides);

    $path = tempnam(sys_get_temp_dir(), 'photo_').'.jpg';
    $img = imagecreatetruecolor(800, 600);
    imagefilledrectangle($img, 0, 0, 800, 600, imagecolorallocate($img, 128, 128, 128));
    imagejpeg($img, $path, 80);
    imagedestroy($img);

    $photo->addMedia($path)
        ->preservingOriginal() // NB: persist media even if source tmp is cleaned
        ->toMediaCollection('image');

    if (is_file($path)) {
        @unlink($path);
    }

    return $photo;
}

it('renders the visuals index page', function () {
    photoWithImage(['title' => ['tr' => 'İstanbul Boğaz sabah kaydı']]);

    $response = $this->get('/goruntu');
    $response->assertOk();
    $response->assertSee('Görüntü');
    $response->assertSee('İstanbul Boğaz sabah kaydı');
});

it('renders an empty-state message when no photos exist', function () {
    $response = $this->get('/goruntu');
    $response->assertOk();
    $response->assertSee('Arşiv boş', false);
});

it('filters the index by kind via query string', function () {
    photoWithImage(['title' => ['tr' => 'DroneA'], 'kind' => 'drone']);
    photoWithImage(['title' => ['tr' => 'PortreA'], 'kind' => 'portrait']);

    $response = $this->get('/goruntu?kind=drone');
    $response->assertOk();
    $response->assertSee('DroneA');
    $response->assertDontSee('PortreA');
});

it('shows a single photo by slug', function () {
    $photo = photoWithImage(['title' => ['tr' => 'Sabah haber masası']]);
    $slug = $photo->getTranslation('slug', 'tr');

    $response = $this->get('/goruntu/'.$slug);
    $response->assertOk();
    $response->assertSee('Sabah haber masası');
    $response->assertSee($photo->resolvedCredit());
});

it('returns 404 for an unknown slug', function () {
    $this->get('/goruntu/does-not-exist')->assertNotFound();
});

it('does not list draft photos on the public index', function () {
    // Draft, with real image attached — must still be excluded
    $photo = photoWithImage(['title' => ['tr' => 'Taslak']]);
    $photo->forceFill(['is_published' => false])->save();

    $this->get('/goruntu')
        ->assertOk()
        ->assertDontSee('Taslak');
});

it('skips photos without an image from the public index', function () {
    Photo::factory()->published()->create(['title' => ['tr' => 'Görselsiz']]);

    $response = $this->get('/goruntu');
    $response->assertOk();
    $response->assertDontSee('Görselsiz');
});

it('shows the Görüntü link in the public nav when enabled', function () {
    SettingsRepository::set('nav.show_visuals', true, 'nav');

    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('/goruntu', false);
});

it('hides the Görüntü link when nav.show_visuals is false', function () {
    SettingsRepository::set('nav.show_visuals', false, 'nav');

    $response = $this->get('/');
    $response->assertOk();
    $response->assertDontSee('href="/goruntu"', false);
});
