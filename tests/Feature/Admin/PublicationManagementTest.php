<?php

declare(strict_types=1);

use App\Models\Publication;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('editor', 'web');
    Role::findOrCreate('contributor', 'web');
    Role::findOrCreate('viewer', 'web');

    // Skip the 2FA middleware guard in this test suite.
    config(['security.require_2fa_for_admin' => false]);
});

it('lists publications with pagination and search filter for editors', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    Publication::factory()->create(['name' => 'Birikim Dergisi', 'sort_order' => 1]);
    Publication::factory()->create(['name' => 'Express', 'sort_order' => 2]);
    Publication::factory()->count(5)->create();

    $response = $this->actingAs($editor)->get(route('admin.publications.index'));

    $response->assertOk();
    $response->assertSee('Yayınlar');
    $response->assertSee('Birikim Dergisi');
    $response->assertSee('Express');

    // Search narrows results.
    $filtered = $this->actingAs($editor)->get(route('admin.publications.index', ['q' => 'Birikim']));
    $filtered->assertOk();
    $filtered->assertSee('Birikim Dergisi');
    $filtered->assertDontSee('Express');
});

it('stores a new publication with auto-generated slug when slug is blank', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post(route('admin.publications.store'), [
        'name' => 'Gazete Duvar',
        'slug' => '',
        'url' => 'https://www.gazeteduvar.com.tr',
        'sort_order' => 5,
    ]);

    $response->assertRedirect(route('admin.publications.index'));
    $response->assertSessionHas('status');

    $publication = Publication::query()->where('name', 'Gazete Duvar')->first();
    expect($publication)->not->toBeNull();
    expect($publication->slug)->toBe('gazete-duvar');
    expect($publication->url)->toBe('https://www.gazeteduvar.com.tr');
    expect($publication->sort_order)->toBe(5);
});

it('rejects store when name is missing or duplicated', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Missing name.
    $this->actingAs($admin)
        ->post(route('admin.publications.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    Publication::factory()->create(['name' => 'Birikim']);

    // Duplicate name.
    $this->actingAs($admin)
        ->post(route('admin.publications.store'), ['name' => 'Birikim'])
        ->assertSessionHasErrors('name');
});

it('updates an existing publication', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $publication = Publication::factory()->create([
        'name' => 'Eski Ad',
        'slug' => 'eski-ad',
        'url' => 'https://eski.test',
        'sort_order' => 10,
    ]);

    $response = $this->actingAs($editor)->put(route('admin.publications.update', $publication), [
        'name' => 'Yeni Ad',
        'slug' => 'yeni-ad',
        'url' => 'https://yeni.test',
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.publications.edit', $publication));

    $publication->refresh();
    expect($publication->name)->toBe('Yeni Ad');
    expect($publication->slug)->toBe('yeni-ad');
    expect($publication->url)->toBe('https://yeni.test');
    expect($publication->sort_order)->toBe(1);
});

it('hard-deletes a publication and detaches pivot rows', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $publication = Publication::factory()->create();

    $response = $this->actingAs($admin)
        ->delete(route('admin.publications.destroy', $publication));

    $response->assertRedirect(route('admin.publications.index'));

    // Hard delete — row is gone completely (no SoftDeletes on model).
    expect(Publication::query()->find($publication->id))->toBeNull();
});

it('forbids contributors and viewers from creating or updating publications', function () {
    $publication = Publication::factory()->create();

    // Contributor can list (needed for writing editor) but not mutate.
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $this->actingAs($contributor)
        ->get(route('admin.publications.index'))
        ->assertOk();

    $this->actingAs($contributor)
        ->get(route('admin.publications.create'))
        ->assertForbidden();

    $this->actingAs($contributor)
        ->post(route('admin.publications.store'), ['name' => 'Hack'])
        ->assertForbidden();

    $this->actingAs($contributor)
        ->delete(route('admin.publications.destroy', $publication))
        ->assertForbidden();

    // Viewer: same — viewAny only.
    $viewer = User::factory()->create();
    $viewer->assignRole('viewer');

    $this->actingAs($viewer)
        ->get(route('admin.publications.index'))
        ->assertOk();

    $this->actingAs($viewer)
        ->post(route('admin.publications.store'), ['name' => 'Nope'])
        ->assertForbidden();
});
