<?php

declare(strict_types=1);

use App\Models\ContactMessage;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['super-admin', 'admin', 'editor', 'contributor', 'viewer'] as $role) {
        Role::findOrCreate($role, 'web');
    }
    config(['security.require_2fa_for_admin' => false]);
});

it('lets admin view contact messages index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/admin/contact')
        ->assertOk();
});

it('lets editor view contact messages index', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $this->actingAs($editor)
        ->get('/admin/contact')
        ->assertOk();
});

it('forbids contributor from contact messages', function () {
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $this->actingAs($contributor)
        ->get('/admin/contact')
        ->assertForbidden();
});

it('forbids viewer from contact messages', function () {
    $viewer = User::factory()->create();
    $viewer->assignRole('viewer');

    $this->actingAs($viewer)
        ->get('/admin/contact')
        ->assertForbidden();
});

it('lets editor update message status but not delete', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');
    $msg = ContactMessage::query()->create([
        'name' => 'Tester',
        'email' => 'a@b.test',
        'body' => 'hi',
        'status' => 'new',
    ]);

    $this->actingAs($editor)
        ->patch("/admin/contact/{$msg->id}", ['status' => 'replied'])
        ->assertRedirect();

    expect($msg->fresh()->status)->toBe('replied');

    $this->actingAs($editor)
        ->delete("/admin/contact/{$msg->id}")
        ->assertForbidden();
});

it('lets admin delete a message', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $msg = ContactMessage::query()->create([
        'name' => 'X',
        'email' => 'x@y.test',
        'body' => 'x',
        'status' => 'new',
    ]);

    $this->actingAs($admin)
        ->delete("/admin/contact/{$msg->id}")
        ->assertRedirect('/admin/contact');

    expect(ContactMessage::query()->find($msg->id))->toBeNull();
});
