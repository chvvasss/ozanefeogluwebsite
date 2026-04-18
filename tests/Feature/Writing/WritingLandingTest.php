<?php

declare(strict_types=1);

use App\Models\Publication;
use App\Models\Writing;

it('loads the landing page with seeded writings', function () {
    $hero = Writing::factory()->featured()->create([
        'title' => ['tr' => 'Öne çıkan yazı'],
        'slug'  => ['tr' => 'one-cikan-yazi'],
    ]);

    Writing::factory()->count(3)->create();

    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('Öne çıkan yazı');
});

it('renders the bylines strip from publications', function () {
    Publication::query()->create([
        'name'       => 'The Custom Herald',
        'slug'       => 'the-custom-herald',
        'url'        => null,
        'sort_order' => 1,
    ]);

    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('The Custom Herald');
});
