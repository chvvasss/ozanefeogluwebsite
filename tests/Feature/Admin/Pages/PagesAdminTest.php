<?php

declare(strict_types=1);

use App\Models\Page;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['super-admin', 'admin', 'editor', 'viewer'] as $role) {
        Role::findOrCreate($role, 'web');
    }
    config(['security.require_2fa_for_admin' => false]);

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');
});

it('lists pages for authorised users', function () {
    Page::query()->create([
        'slug' => 'hakkimda', 'kind' => 'system', 'template' => 'about',
        'title' => ['tr' => 'Hakkında'], 'is_published' => true,
    ]);

    $this->actingAs($this->editor)->get('/admin/pages')->assertOk()->assertSee('Hakkında');
});

it('forbids viewers from creating', function () {
    $viewer = User::factory()->create();
    $viewer->assignRole('viewer');

    $this->actingAs($viewer)->get('/admin/pages/create')->assertForbidden();
});

it('updates a system page body without changing slug', function () {
    $page = Page::query()->create([
        'slug' => 'hakkimda', 'kind' => 'system', 'template' => 'about',
        'title' => ['tr' => 'Hakkında'], 'body' => ['tr' => '<p>old</p>'],
        'is_published' => true,
    ]);

    $this->actingAs($this->editor)->put("/admin/pages/{$page->id}", [
        'title_tr' => 'Hakkında (yeni)',
        'slug' => 'baska-slug',  // should be ignored for system page
        'template' => 'default',     // should be ignored
        'intro_tr' => 'Yeni intro',
        'body_tr' => '<p>new body</p>',
        'is_published' => '1',
    ])->assertRedirect();

    $page->refresh();
    expect($page->slug)->toBe('hakkimda')
        ->and($page->template)->toBe('about')
        ->and($page->getTranslation('title', 'tr', false))->toBe('Hakkında (yeni)')
        ->and($page->getTranslation('intro', 'tr', false))->toBe('Yeni intro');
});

it('rejects deleting a system page', function () {
    $page = Page::query()->create([
        'slug' => 'iletisim', 'kind' => 'system', 'template' => 'contact',
        'title' => ['tr' => 'İletişim'], 'body' => ['tr' => '<p>x</p>'],
        'is_published' => true,
    ]);

    $this->actingAs($this->editor)
        ->delete("/admin/pages/{$page->id}")
        ->assertForbidden();

    expect(Page::query()->find($page->id))->not->toBeNull();
});

it('creates a custom page', function () {
    $this->actingAs($this->editor)->post('/admin/pages', [
        'title_tr' => 'Renkli Sayfa',
        'slug' => 'renkli-sayfa',
        'template' => 'default',
        'body_tr' => '<p>içerik</p>',
        'is_published' => '1',
    ])->assertRedirect();

    $page = Page::query()->where('slug', 'renkli-sayfa')->first();
    expect($page)->not->toBeNull()
        ->and($page->kind)->toBe('custom');
});

it('rejects malformed slugs', function () {
    $this->actingAs($this->editor)->from('/admin/pages/create')
        ->post('/admin/pages', [
            'title_tr' => 'X',
            'slug' => 'NOT VALID',
            'template' => 'default',
            'body_tr' => '<p>x</p>',
            'is_published' => '1',
        ])->assertSessionHasErrors('slug');
});
