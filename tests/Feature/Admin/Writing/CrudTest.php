<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Writing;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['super-admin', 'admin', 'editor', 'contributor'] as $role) {
        Role::findOrCreate($role, 'web');
    }
    config(['security.require_2fa_for_admin' => false]);

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');
});

it('creates a writing with minimal valid payload', function () {
    $response = $this->actingAs($this->editor)->post('/admin/writings', [
        'title_tr' => 'Deneme başlığı',
        'slug_tr' => '',
        'kind' => 'deneme',
        'status' => 'draft',
        'body_tr' => '<p>Bir gövde paragrafı.</p>',
        'cover_hue_a' => 24,
        'cover_hue_b' => 200,
    ]);

    $response->assertRedirect();
    expect(Writing::count())->toBe(1);

    $writing = Writing::first();
    expect($writing->getTranslation('title', 'tr', false))->toBe('Deneme başlığı')
        ->and($writing->getTranslation('slug', 'tr', false))->toBe('deneme-basligi')
        ->and($writing->author_id)->toBe($this->editor->id)
        ->and($writing->status)->toBe('draft');
});

it('auto-generates unique slugs', function () {
    Writing::factory()->create(['slug' => ['tr' => 'deneme-basligi']]);

    $this->actingAs($this->editor)->post('/admin/writings', [
        'title_tr' => 'Deneme başlığı',
        'slug_tr' => '',
        'kind' => 'deneme',
        'status' => 'draft',
        'body_tr' => '<p>Gövde.</p>',
        'cover_hue_a' => 24,
        'cover_hue_b' => 200,
    ])->assertRedirect();

    $new = Writing::query()->where('id', '!=', Writing::first()->id)->first();
    expect($new->getTranslation('slug', 'tr', false))->toBe('deneme-basligi-2');
});

it('transliterates Turkish characters into ASCII slugs', function () {
    $this->actingAs($this->editor)->post('/admin/writings', [
        'title_tr' => 'Şifre, güneş ve çiğ — İstanbul üstüne',
        'slug_tr' => '',
        'kind' => 'deneme',
        'status' => 'draft',
        'body_tr' => '<p>Kısa gövde.</p>',
        'cover_hue_a' => 24,
        'cover_hue_b' => 200,
    ])->assertRedirect();

    $writing = Writing::first();
    expect($writing->getTranslation('slug', 'tr', false))
        ->toBe('sifre-gunes-ve-cig-istanbul-ustune');
});

it('updates an existing writing', function () {
    $writing = Writing::factory()->create([
        'title' => ['tr' => 'Eski başlık'],
        'slug' => ['tr' => 'eski-baslik'],
    ]);

    $this->actingAs($this->editor)->put("/admin/writings/{$writing->id}", [
        'title_tr' => 'Yeni başlık',
        'slug_tr' => 'eski-baslik',
        'kind' => $writing->kind,
        'status' => 'published',
        'body_tr' => '<p>Yenilenmiş gövde metni.</p>',
        'cover_hue_a' => $writing->cover_hue_a,
        'cover_hue_b' => $writing->cover_hue_b,
    ])->assertRedirect();

    $writing->refresh();
    expect($writing->getTranslation('title', 'tr', false))->toBe('Yeni başlık')
        ->and($writing->status)->toBe('published');
});

it('sanitizes script tags out of body on save', function () {
    $payload = '<p>Ok</p><script>alert(1)</script><p>Sonra</p>';

    $this->actingAs($this->editor)->post('/admin/writings', [
        'title_tr' => 'XSS deneme',
        'slug_tr' => '',
        'kind' => 'deneme',
        'status' => 'draft',
        'body_tr' => $payload,
        'cover_hue_a' => 24,
        'cover_hue_b' => 200,
    ])->assertRedirect();

    $body = Writing::first()->getTranslation('body', 'tr', false);
    expect($body)->not->toContain('<script>')
        ->and($body)->not->toContain('alert(1)')
        ->and($body)->toContain('<p>Ok</p>');
});

it('publishes and unpublishes via toggle endpoints', function () {
    $writing = Writing::factory()->draft()->create();

    $this->actingAs($this->editor)
        ->post("/admin/writings/{$writing->id}/publish")
        ->assertRedirect();

    $writing->refresh();
    expect($writing->status)->toBe('published')
        ->and($writing->published_at)->not->toBeNull();

    $this->actingAs($this->editor)
        ->post("/admin/writings/{$writing->id}/unpublish")
        ->assertRedirect();

    $writing->refresh();
    expect($writing->status)->toBe('draft');
});

it('soft-deletes a writing', function () {
    $writing = Writing::factory()->create();

    $this->actingAs($this->editor)
        ->delete("/admin/writings/{$writing->id}")
        ->assertRedirect();

    expect(Writing::query()->find($writing->id))->toBeNull()
        ->and(Writing::query()->withTrashed()->find($writing->id))->not->toBeNull();
});

it('forces draft status when contributor tries to publish directly', function () {
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $this->actingAs($contributor)->post('/admin/writings', [
        'title_tr' => 'Contributor hile',
        'slug_tr' => '',
        'kind' => 'deneme',
        'status' => 'published',
        'body_tr' => '<p>Gövde.</p>',
        'cover_hue_a' => 24,
        'cover_hue_b' => 200,
    ])->assertRedirect();

    expect(Writing::first()->status)->toBe('draft');
});
