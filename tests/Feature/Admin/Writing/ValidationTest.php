<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['editor'] as $role) {
        Role::findOrCreate($role, 'web');
    }
    config(['security.require_2fa_for_admin' => false]);

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');
});

it('requires title and body', function () {
    $response = $this->actingAs($this->editor)->from('/admin/writings/create')
        ->post('/admin/writings', [
            'title_tr' => '',
            'kind' => 'deneme',
            'status' => 'draft',
            'body_tr' => '',
            'cover_hue_a' => 24,
            'cover_hue_b' => 200,
        ]);

    $response->assertSessionHasErrors(['title_tr', 'body_tr']);
});

it('rejects an invalid kind', function () {
    $response = $this->actingAs($this->editor)->from('/admin/writings/create')
        ->post('/admin/writings', [
            'title_tr' => 'X',
            'kind' => 'saldiri',
            'status' => 'draft',
            'body_tr' => '<p>X</p>',
            'cover_hue_a' => 24,
            'cover_hue_b' => 200,
        ]);

    $response->assertSessionHasErrors('kind');
});

it('rejects an invalid status', function () {
    $response = $this->actingAs($this->editor)->from('/admin/writings/create')
        ->post('/admin/writings', [
            'title_tr' => 'X',
            'kind' => 'deneme',
            'status' => 'unknown',
            'body_tr' => '<p>X</p>',
            'cover_hue_a' => 24,
            'cover_hue_b' => 200,
        ]);

    $response->assertSessionHasErrors('status');
});

it('rejects a badly formed slug', function () {
    $response = $this->actingAs($this->editor)->from('/admin/writings/create')
        ->post('/admin/writings', [
            'title_tr' => 'X',
            'slug_tr' => 'Boşluklu Slug!',
            'kind' => 'deneme',
            'status' => 'draft',
            'body_tr' => '<p>X</p>',
            'cover_hue_a' => 24,
            'cover_hue_b' => 200,
        ]);

    $response->assertSessionHasErrors('slug_tr');
});

it('rejects out-of-range cover hues', function () {
    $response = $this->actingAs($this->editor)->from('/admin/writings/create')
        ->post('/admin/writings', [
            'title_tr' => 'X',
            'kind' => 'deneme',
            'status' => 'draft',
            'body_tr' => '<p>X</p>',
            'cover_hue_a' => 500,
            'cover_hue_b' => -1,
        ]);

    $response->assertSessionHasErrors(['cover_hue_a', 'cover_hue_b']);
});
