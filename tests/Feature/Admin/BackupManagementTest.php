<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('super-admin', 'web');
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('contributor', 'web');

    config(['security.require_2fa_for_admin' => false]);

    // Fake the configured backup disk so tests never touch real storage.
    $disk = (array) config('backup.backup.destination.disks', ['local']);
    Storage::fake($disk[0] ?? 'local');
});

function backupDisk(): string
{
    $disks = (array) config('backup.backup.destination.disks', ['local']);

    return (string) ($disks[0] ?? 'local');
}

function backupPath(): string
{
    return (string) config('backup.backup.name', 'laravel-backup');
}

it('renders the backup index page for an admin user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Seed a fake backup file to assert the listing renders it.
    Storage::disk(backupDisk())->put(
        backupPath().'/2026-04-24-10-00-00.zip',
        'fake-zip-content'
    );

    $response = $this->actingAs($user)->get('/admin/backup');

    $response->assertOk();
    $response->assertSee('Yedekleme');
    $response->assertSee('2026-04-24-10-00-00.zip');
});

it('forbids backup access for a contributor', function () {
    $user = User::factory()->create();
    $user->assignRole('contributor');

    $this->actingAs($user)
        ->get('/admin/backup')
        ->assertForbidden();
});

it('triggers the backup:run artisan command when admin posts to store', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Artisan::shouldReceive('call')
        ->once()
        ->with('backup:run', ['--only-db' => true])
        ->andReturn(0);

    $this->actingAs($user)
        ->post('/admin/backup')
        ->assertRedirect();
});

it('returns a file response when admin downloads a backup', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $filename = '2026-04-24-12-00-00.zip';
    Storage::disk(backupDisk())->put(backupPath().'/'.$filename, 'fake-zip-content');

    $response = $this->actingAs($user)
        ->get('/admin/backup/download/'.$filename);

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename='.$filename);
});

it('deletes a backup file when admin submits destroy', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $filename = '2026-04-24-13-00-00.zip';
    $full = backupPath().'/'.$filename;
    Storage::disk(backupDisk())->put($full, 'fake-zip-content');

    $this->actingAs($user)
        ->delete('/admin/backup/'.$filename)
        ->assertRedirect(route('admin.backup.index'));

    expect(Storage::disk(backupDisk())->exists($full))->toBeFalse();
});

it('rejects path traversal filenames with 404', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get('/admin/backup/download/..%2F..%2Fsecret.zip')
        ->assertNotFound();
});
