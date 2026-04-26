<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-backups');

        $disk = $this->disk();
        $path = $this->path();

        $filesystem = Storage::disk($disk);
        $files = $filesystem->exists($path)
            ? collect($filesystem->files($path))
            : collect();

        $backups = $files
            ->filter(fn (string $f) => str_ends_with($f, '.zip'))
            ->map(fn (string $f) => [
                'name' => basename($f),
                'path' => $f,
                'size' => $filesystem->size($f),
                'last_modified' => $filesystem->lastModified($f),
            ])
            ->sortByDesc('last_modified')
            ->values();

        $totalBytes = $backups->sum('size');

        return view('admin.backup.index', [
            'backups' => $backups->take(10),
            'totalBackups' => $backups->count(),
            'totalBytes' => $totalBytes,
            'latestAt' => $backups->first()['last_modified'] ?? null,
            'disk' => $disk,
        ]);
    }

    public function create(): RedirectResponse
    {
        $this->authorize('manage-backups');

        try {
            // Sync execution — DB-only for first iteration; files added later.
            Artisan::call('backup:run', ['--only-db' => true]);

            return back()->with('status', __('Yedekleme başlatıldı.'));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', __('Yedekleme başarısız: :msg', ['msg' => $e->getMessage()]));
        }
    }

    public function download(string $filename): StreamedResponse
    {
        $this->authorize('manage-backups');

        $path = $this->resolveFile($filename);

        return Storage::disk($this->disk())->download($path, $filename);
    }

    public function destroy(string $filename): RedirectResponse
    {
        $this->authorize('manage-backups');

        $path = $this->resolveFile($filename);

        Storage::disk($this->disk())->delete($path);

        return redirect()
            ->route('admin.backup.index')
            ->with('status', __('Yedek silindi.'));
    }

    private function disk(): string
    {
        $disks = (array) config('backup.backup.destination.disks', ['local']);

        return (string) ($disks[0] ?? 'local');
    }

    private function path(): string
    {
        // Spatie default path uses the backup name (APP_NAME).
        return (string) config('backup.backup.name', 'laravel-backup');
    }

    /**
     * Resolve a user-supplied filename against the backup path, preventing
     * path traversal and ensuring the file exists on the configured disk.
     */
    private function resolveFile(string $filename): string
    {
        $safe = basename($filename);
        abort_if($safe !== $filename || $safe === '' || ! str_ends_with($safe, '.zip'), 404);

        $path = $this->path().'/'.$safe;

        abort_unless(Storage::disk($this->disk())->exists($path), 404);

        return $path;
    }
}
