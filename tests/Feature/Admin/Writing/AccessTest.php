<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Writing;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['super-admin', 'admin', 'editor', 'contributor', 'viewer'] as $role) {
        Role::findOrCreate($role, 'web');
    }
    config(['security.require_2fa_for_admin' => false]);
});

it('redirects guests to login', function () {
    $this->get('/admin/writings')->assertRedirect('/admin/login');
    $this->get('/admin/writings/create')->assertRedirect('/admin/login');
});

it('allows super-admin full access', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user)->get('/admin/writings')->assertOk();
    $this->actingAs($user)->get('/admin/writings/create')->assertOk();
});

it('allows admin full access', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)->get('/admin/writings')->assertOk();
    $this->actingAs($user)->get('/admin/writings/create')->assertOk();
});

it('allows editor to view list and create', function () {
    $user = User::factory()->create();
    $user->assignRole('editor');

    $this->actingAs($user)->get('/admin/writings')->assertOk();
    $this->actingAs($user)->get('/admin/writings/create')->assertOk();
});

it('allows contributor to create drafts', function () {
    $user = User::factory()->create();
    $user->assignRole('contributor');

    $this->actingAs($user)->get('/admin/writings')->assertOk();
    $this->actingAs($user)->get('/admin/writings/create')->assertOk();
});

it('forbids viewer from creating', function () {
    $user = User::factory()->create();
    $user->assignRole('viewer');

    $this->actingAs($user)->get('/admin/writings')->assertOk();
    $this->actingAs($user)->get('/admin/writings/create')->assertForbidden();
});

it('prevents contributor from editing another user published writing', function () {
    $owner = User::factory()->create();
    $owner->assignRole('editor');
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $writing = Writing::factory()->create(['author_id' => $owner->id, 'status' => 'published']);

    $this->actingAs($contributor)->get("/admin/writings/{$writing->id}/edit")->assertForbidden();
});

it('allows contributor to edit own draft', function () {
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $writing = Writing::factory()->draft()->create(['author_id' => $contributor->id]);

    $this->actingAs($contributor)->get("/admin/writings/{$writing->id}/edit")->assertOk();
});

it('prevents contributor from publishing', function () {
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $writing = Writing::factory()->draft()->create(['author_id' => $contributor->id]);

    $this->actingAs($contributor)
        ->post("/admin/writings/{$writing->id}/publish")
        ->assertForbidden();
});

it('allows editor to publish', function () {
    $user = User::factory()->create();
    $user->assignRole('editor');

    $writing = Writing::factory()->draft()->create();

    $this->actingAs($user)
        ->post("/admin/writings/{$writing->id}/publish")
        ->assertRedirect();

    $writing->refresh();
    expect($writing->status)->toBe('published');
});
