<?php

declare(strict_types=1);

use App\Models\Page;
use Database\Seeders\LegalPageSeeder;

beforeEach(function (): void {
    $this->seed(LegalPageSeeder::class);
});

it('renders the KVKK legal page from the database', function () {
    $response = $this->get('/hukuksal/kvkk');
    $response->assertOk();
    $response->assertSee('KVKK');
});

it('renders the gizlilik legal page', function () {
    $this->get('/hukuksal/gizlilik')->assertOk();
});

it('returns 404 for an unknown legal slug', function () {
    $this->get('/hukuksal/bilinmeyen')->assertNotFound();
});

it('returns 404 when a legal page is unpublished', function () {
    Page::query()->where('slug', 'kvkk')->update(['is_published' => false]);

    $this->get('/hukuksal/kvkk')->assertNotFound();
});

it('redirects /kvkk to /hukuksal/kvkk for backward compat', function () {
    $this->get('/kvkk')->assertRedirect('/hukuksal/kvkk');
});
