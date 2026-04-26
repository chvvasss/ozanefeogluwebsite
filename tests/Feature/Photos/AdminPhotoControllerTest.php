<?php

declare(strict_types=1);

use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('editor', 'web');
    Role::findOrCreate('contributor', 'web');
    config(['security.require_2fa_for_admin' => false]);
    Storage::fake('public');
});

it('lists photos in the admin index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Photo::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get('/admin/photos')
        ->assertOk()
        ->assertSee('Fotoğraflar');
});

it('requires authentication for admin photo routes', function () {
    $this->get('/admin/photos')->assertRedirect();
    $this->get('/admin/photos/create')->assertRedirect();
});

it('creates a photo with an image upload', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post('/admin/photos', [
        'title_tr' => 'İstanbul sabah kaydı',
        'slug_tr' => 'istanbul-sabah-kaydi',
        'kind' => 'reportage',
        'license' => 'editorial-only',
        'source' => 'AA',
        'is_published' => '1',
        'hero_eligible' => '0',
        'is_featured' => '0',
        'image' => File::image('new.jpg', 1200, 800),
    ]);

    $response->assertSessionHasNoErrors();
    $photo = Photo::query()->where('slug->tr', 'istanbul-sabah-kaydi')->firstOrFail();
    expect($photo->getTranslation('title', 'tr'))->toBe('İstanbul sabah kaydı');
    expect($photo->is_published)->toBeTrue();
    expect($photo->hasImage())->toBeTrue();

    $response->assertRedirect(route('admin.photos.edit', $photo));
});

it('auto-generates slug when slug_tr is blank', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->post('/admin/photos', [
        'title_tr' => 'Yeni Drone Hattı',
        'kind' => 'drone',
        'is_published' => '0',
    ])->assertSessionHasNoErrors();

    $photo = Photo::query()->first();
    expect($photo->getTranslation('slug', 'tr'))->toBe('yeni-drone-hatti');
});

it('rejects invalid kind and license values', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->post('/admin/photos', [
        'title_tr' => 'X',
        'kind' => 'invalid-kind',
        'license' => 'pirate',
    ])->assertSessionHasErrors(['kind', 'license']);
});

it('updates an existing photo', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $photo = Photo::factory()->create(['title' => ['tr' => 'Eski']]);

    $this->actingAs($admin)->put('/admin/photos/'.$photo->id, [
        'title_tr' => 'Yeni başlık',
        'kind' => $photo->kind,
        'is_published' => '1',
    ])->assertRedirect(route('admin.photos.edit', $photo));

    $photo->refresh();
    expect($photo->getTranslation('title', 'tr'))->toBe('Yeni başlık');
    expect($photo->is_published)->toBeTrue();
});

it('soft-deletes a photo on destroy', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $photo = Photo::factory()->create();

    $this->actingAs($admin)->delete('/admin/photos/'.$photo->id)
        ->assertRedirect(route('admin.photos.index'));

    expect(Photo::query()->withTrashed()->find($photo->id)->trashed())->toBeTrue();
});

it('allows a contributor to create but not edit someone elses photo', function () {
    $author = User::factory()->create();
    $author->assignRole('contributor');
    $outsider = User::factory()->create();
    $outsider->assignRole('contributor');

    $photo = Photo::factory()->create(['created_by' => $author->id, 'is_published' => false]);

    $this->actingAs($outsider)
        ->get('/admin/photos/'.$photo->id.'/edit')
        ->assertForbidden();
});

it('bulk-uploads multiple photos as drafts', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post('/admin/photos/bulk-upload', [
        'images' => [
            File::image('street-one.jpg', 800, 600),
            File::image('square-two.jpg', 1000, 1000),
        ],
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    expect(Photo::count())->toBe(2);
    expect(Photo::query()->pluck('is_published'))->each->toBeFalse();

    $first = Photo::query()->first();
    expect($first->getTranslation('title', 'tr'))->toContain('street');
    expect($first->hasImage())->toBeTrue();
});

it('rejects a bulk upload with too many files', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $files = [];
    for ($i = 0; $i < 21; $i++) {
        $files[] = File::image("f{$i}.jpg", 100, 100);
    }

    $this->actingAs($admin)
        ->post('/admin/photos/bulk-upload', ['images' => $files])
        ->assertSessionHasErrors('images');
});

it('forbids a contributor from editing a published photo they own', function () {
    $author = User::factory()->create();
    $author->assignRole('contributor');

    $photo = Photo::factory()->create([
        'created_by' => $author->id,
        'is_published' => true,
    ]);

    $this->actingAs($author)
        ->put('/admin/photos/'.$photo->id, [
            'title_tr' => 'try',
            'kind' => $photo->kind,
        ])
        ->assertForbidden();
});
