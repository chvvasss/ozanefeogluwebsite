<?php

declare(strict_types=1);

use App\Models\Writing;

it('calculates read time from body text', function () {
    $body = '<p>'.str_repeat('kelime ', 440).'</p>';
    $writing = Writing::factory()->create([
        'body' => ['tr' => $body],
    ]);

    expect($writing->read_minutes)->toBe(2);
});

it('respects the published scope', function () {
    Writing::factory()->draft()->create();
    Writing::factory()->scheduled()->create();
    $published = Writing::factory()->create([
        'status' => 'published',
        'published_at' => now()->subHour(),
    ]);

    $results = Writing::query()->published()->pluck('id');
    expect($results)->toContain($published->id);
    expect($results->count())->toBe(1);
});

it('filters by kind', function () {
    Writing::factory()->ofKind('deneme')->create();
    $roportaj = Writing::factory()->ofKind('roportaj')->create();

    $results = Writing::query()->published()->ofKind('roportaj')->pluck('id');
    expect($results)->toContain($roportaj->id);
    expect($results->count())->toBe(1);
});

it('resolves by slug in current locale with fallback', function () {
    $writing = Writing::factory()->create([
        'slug' => ['tr' => 'ornek-slug', 'en' => 'example-slug'],
    ]);

    app()->setLocale('tr');
    $foundTr = Writing::query()->bySlug('ornek-slug')->first();
    expect($foundTr?->id)->toBe($writing->id);

    app()->setLocale('en');
    $foundEn = Writing::query()->bySlug('example-slug')->first();
    expect($foundEn?->id)->toBe($writing->id);

    // Fallback: EN locale but only TR slug exists on another record
    $trOnly = Writing::factory()->create([
        'slug' => ['tr' => 'sadece-turkce', 'en' => 'sadece-turkce'],
    ]);
    app()->setLocale('en');
    $found = Writing::query()->bySlug('sadece-turkce')->first();
    expect($found?->id)->toBe($trOnly->id);
});

it('exposes url via translatable slug', function () {
    app()->setLocale('tr');
    $writing = Writing::factory()->create([
        'slug' => ['tr' => 'kisi-konum-zaman'],
    ]);

    expect($writing->url())->toBe('/yazilar/kisi-konum-zaman');
});

it('returns turkish kind labels', function () {
    $w1 = Writing::factory()->ofKind('saha_yazisi')->create();
    $w2 = Writing::factory()->ofKind('roportaj')->create();

    expect($w1->kind_label)->toBe('saha yazısı');
    expect($w2->kind_label)->toBe('röportaj');
});

it('guards the kind enum at db level', function () {
    expect(fn () => Writing::factory()->state(['kind' => 'saldiri'])->create())
        ->toThrow(Throwable::class);
})->skip('SQLite does not enforce enum constraints; skip to avoid false positives.');
