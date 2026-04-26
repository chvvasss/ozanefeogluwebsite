<?php

declare(strict_types=1);

use App\Models\Page;

it('renders the about page when seeded', function () {
    Page::query()->create([
        'slug' => 'hakkimda',
        'kind' => 'system',
        'template' => 'about',
        'title' => ['tr' => 'Hakkında'],
        'intro' => ['tr' => 'Kısa bir tanıtım.'],
        'body' => ['tr' => '<p>Test gövde metni.</p>'],
        'extras' => ['credentials' => [['label' => 'üs', 'value' => 'İstanbul']]],
        'is_published' => true,
    ]);

    $response = $this->get('/hakkimda');
    $response->assertOk();
    $response->assertSee('Kısa bir tanıtım', false);
});

it('returns 404 when about page is not published', function () {
    Page::query()->create([
        'slug' => 'hakkimda',
        'kind' => 'system',
        'template' => 'about',
        'title' => ['tr' => 'Hakkında'],
        'body' => ['tr' => '<p>x</p>'],
        'is_published' => false,
    ]);

    $this->get('/hakkimda')->assertNotFound();
});

it('renders the contact page with its real email', function () {
    // Contact page is now config-driven; e-mail comes from config('site.contact.email')
    config()->set('site.contact.email', 'press@example.com');

    Page::query()->create([
        'slug' => 'iletisim',
        'kind' => 'system',
        'template' => 'contact',
        'title' => ['tr' => 'Yazışma'],
        'intro' => ['tr' => 'Kanallar.'],
        'body' => ['tr' => '<p>not.</p>'],
        'is_published' => true,
    ]);

    $response = $this->get('/iletisim');
    $response->assertOk();
    $response->assertSee('press@example.com');
    $response->assertSee('Yazın.', false);
});
