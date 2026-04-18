<?php

declare(strict_types=1);

use App\Models\Writing;

it('lists published writings on the index', function () {
    $published = Writing::factory()->create([
        'title' => ['tr' => 'Yayında bir yazı'],
        'slug'  => ['tr' => 'yayinda-bir-yazi'],
    ]);
    Writing::factory()->draft()->create([
        'title' => ['tr' => 'Taslak yazı'],
        'slug'  => ['tr' => 'taslak-yazi'],
    ]);

    $response = $this->get('/yazilar');
    $response->assertOk();
    $response->assertSee('Yayında bir yazı');
    $response->assertDontSee('Taslak yazı');
});

it('filters index by kind via query string', function () {
    Writing::factory()->ofKind('deneme')->create([
        'title' => ['tr' => 'Bir deneme'], 'slug' => ['tr' => 'bir-deneme'],
    ]);
    Writing::factory()->ofKind('roportaj')->create([
        'title' => ['tr' => 'Bir röportaj'], 'slug' => ['tr' => 'bir-roportaj'],
    ]);

    $response = $this->get('/yazilar?tur=roportaj');
    $response->assertOk();
    $response->assertSee('Bir röportaj');
    $response->assertDontSee('Bir deneme');
});

it('ignores an unknown kind filter', function () {
    Writing::factory()->create([
        'title' => ['tr' => 'Geçerli yazı'], 'slug' => ['tr' => 'gecerli-yazi'],
    ]);

    $response = $this->get('/yazilar?tur=saldiri');
    $response->assertOk();
    $response->assertSee('Geçerli yazı');
});

it('shows a single writing by slug', function () {
    $writing = Writing::factory()->create([
        'title'   => ['tr' => 'Tek yazı'],
        'slug'    => ['tr' => 'tek-yazi'],
        'excerpt' => ['tr' => 'Kısa bir özet cümle.'],
        'body'    => ['tr' => '<p>Uzun gövde metni...</p>'],
    ]);

    $response = $this->get('/yazilar/tek-yazi');
    $response->assertOk();
    $response->assertSee('Tek yazı');
    $response->assertSee('Kısa bir özet cümle.');
});

it('returns 404 for unknown slug', function () {
    $response = $this->get('/yazilar/unknown-slug-xyz');
    $response->assertNotFound();
});

it('does not show draft writings on the public show page', function () {
    Writing::factory()->draft()->create([
        'title' => ['tr' => 'Draft'], 'slug' => ['tr' => 'draft'],
    ]);

    $response = $this->get('/yazilar/draft');
    $response->assertNotFound();
});

it('does not show future-scheduled writings', function () {
    Writing::factory()->scheduled()->create([
        'title' => ['tr' => 'Planlı'], 'slug' => ['tr' => 'planli'],
    ]);

    $response = $this->get('/yazilar/planli');
    $response->assertNotFound();
});

it('renders an empty-state message when the filter has no results', function () {
    $response = $this->get('/yazilar?tur=not');
    $response->assertOk();
    $response->assertSee('henüz', false);
});
