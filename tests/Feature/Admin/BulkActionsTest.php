<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\PhotoBulkController;
use App\Http\Controllers\Admin\WritingBulkController;
use App\Models\Photo;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['super-admin', 'admin', 'editor', 'contributor', 'viewer'] as $role) {
        Role::findOrCreate($role, 'web');
    }
    config(['security.require_2fa_for_admin' => false]);

    // The bulk routes live inside the admin group which patron will merge.
    // Register them here so the feature tests exercise the real controller
    // contract without touching routes/web.php.
    Route::middleware(['web', 'auth'])
        ->prefix('admin')
        ->as('admin.')
        ->group(function (): void {
            Route::post('/writings/bulk', WritingBulkController::class)->name('writings.bulk');
            Route::post('/photos/bulk', PhotoBulkController::class)->name('photos.bulk');
        });

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');
});

// ---------------------------------------------------------------------
// Writings
// ---------------------------------------------------------------------

it('bulk publishes multiple draft writings', function () {
    $a = Writing::factory()->draft()->create();
    $b = Writing::factory()->draft()->create();

    $this->actingAs($this->editor)
        ->post('/admin/writings/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'publish',
        ])
        ->assertRedirect();

    expect($a->fresh()->status)->toBe('published')
        ->and($a->fresh()->published_at)->not->toBeNull()
        ->and($b->fresh()->status)->toBe('published')
        ->and($b->fresh()->published_at)->not->toBeNull();
});

it('bulk unpublishes multiple published writings', function () {
    $a = Writing::factory()->create(['status' => 'published']);
    $b = Writing::factory()->create(['status' => 'published']);

    $this->actingAs($this->editor)
        ->post('/admin/writings/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'unpublish',
        ])
        ->assertRedirect();

    expect($a->fresh()->status)->toBe('draft')
        ->and($b->fresh()->status)->toBe('draft');
});

it('bulk features then unfeatures writings', function () {
    $a = Writing::factory()->create(['is_featured' => false]);
    $b = Writing::factory()->create(['is_featured' => false]);

    $this->actingAs($this->editor)
        ->post('/admin/writings/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'feature',
        ])
        ->assertRedirect();

    expect($a->fresh()->is_featured)->toBeTrue()
        ->and($b->fresh()->is_featured)->toBeTrue();

    $this->actingAs($this->editor)
        ->post('/admin/writings/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'unfeature',
        ])
        ->assertRedirect();

    expect($a->fresh()->is_featured)->toBeFalse()
        ->and($b->fresh()->is_featured)->toBeFalse();
});

it('bulk soft-deletes writings', function () {
    $a = Writing::factory()->create();
    $b = Writing::factory()->create();

    $this->actingAs($this->editor)
        ->post('/admin/writings/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'delete',
        ])
        ->assertRedirect();

    expect(Writing::query()->find($a->id))->toBeNull()
        ->and(Writing::query()->find($b->id))->toBeNull()
        ->and(Writing::query()->withTrashed()->find($a->id))->not->toBeNull()
        ->and(Writing::query()->withTrashed()->find($b->id))->not->toBeNull();
});

it('rejects an unknown writings bulk action with a validation error', function () {
    $w = Writing::factory()->create();

    $this->actingAs($this->editor)
        ->post('/admin/writings/bulk', [
            'ids' => [$w->id],
            'action' => 'nuke',
        ])
        ->assertSessionHasErrors('action');
});

it('skips contributor bulk-delete of writings authored by others', function () {
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $someoneElse = User::factory()->create();
    $someoneElse->assignRole('editor');

    $foreignDraft = Writing::factory()->draft()->create(['author_id' => $someoneElse->id]);

    $this->actingAs($contributor)
        ->post('/admin/writings/bulk', [
            'ids' => [$foreignDraft->id],
            'action' => 'delete',
        ])
        ->assertRedirect();

    // Policy blocked — writing must still exist (not soft-deleted).
    expect(Writing::query()->find($foreignDraft->id))->not->toBeNull();
});

// ---------------------------------------------------------------------
// Photos
// ---------------------------------------------------------------------

it('bulk publishes and unpublishes photos', function () {
    $a = Photo::factory()->draft()->create();
    $b = Photo::factory()->draft()->create();

    $this->actingAs($this->editor)
        ->post('/admin/photos/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'publish',
        ])
        ->assertRedirect();

    expect($a->fresh()->is_published)->toBeTrue()
        ->and($b->fresh()->is_published)->toBeTrue();

    $this->actingAs($this->editor)
        ->post('/admin/photos/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'unpublish',
        ])
        ->assertRedirect();

    expect($a->fresh()->is_published)->toBeFalse()
        ->and($b->fresh()->is_published)->toBeFalse();
});

it('bulk marks photos as hero candidates and back', function () {
    $a = Photo::factory()->create(['hero_eligible' => false]);
    $b = Photo::factory()->create(['hero_eligible' => false]);

    $this->actingAs($this->editor)
        ->post('/admin/photos/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'hero',
        ])
        ->assertRedirect();

    expect($a->fresh()->hero_eligible)->toBeTrue()
        ->and($b->fresh()->hero_eligible)->toBeTrue();

    $this->actingAs($this->editor)
        ->post('/admin/photos/bulk', [
            'ids' => [$a->id, $b->id],
            'action' => 'unhero',
        ])
        ->assertRedirect();

    expect($a->fresh()->hero_eligible)->toBeFalse()
        ->and($b->fresh()->hero_eligible)->toBeFalse();
});
