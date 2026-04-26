<?php

declare(strict_types=1);

use App\Models\Photo;

it('respects the published scope', function () {
    Photo::factory()->published()->create();
    Photo::factory()->draft()->create();

    expect(Photo::query()->published()->count())->toBe(1);
});

it('filters by kind via scope', function () {
    Photo::factory()->ofKind('drone')->create();
    Photo::factory()->ofKind('portrait')->create();
    Photo::factory()->ofKind('drone')->create();

    expect(Photo::query()->kind('drone')->count())->toBe(2);
    expect(Photo::query()->kind('portrait')->count())->toBe(1);
    // Invalid kind is a no-op
    expect(Photo::query()->kind('galactic')->count())->toBe(3);
});

it('falls back to site setting for credit when own credit is blank', function () {
    $photo = Photo::factory()->create(['credit' => '']);

    expect($photo->resolvedCredit())->toBe((string) site_setting('photo.default_credit'));
});

it('prefers its own credit over the site default', function () {
    $photo = Photo::factory()->create(['credit' => 'Foto: Test / Freelance']);

    expect($photo->resolvedCredit())->toBe('Foto: Test / Freelance');
});

it('exposes a translatable url built from tr slug', function () {
    $photo = Photo::factory()->create([
        'title' => ['tr' => 'Mini drone hattı', 'en' => 'Mini drone line'],
    ]);

    expect($photo->url())->toContain('/goruntu/');
    expect($photo->url())->toContain($photo->getTranslation('slug', 'tr'));
});

it('returns a Turkish kind label', function () {
    $photo = Photo::factory()->ofKind('drone')->create();
    expect($photo->kind_label)->toBe('drone');

    $p2 = Photo::factory()->ofKind('reportage')->create();
    expect($p2->kind_label)->toBe('röportaj');
});
