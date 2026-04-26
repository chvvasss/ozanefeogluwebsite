<?php

declare(strict_types=1);

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\ContactMessagesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PhotoBulkController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\Admin\SessionsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WritingBulkController;
use App\Http\Controllers\Admin\WritingController as AdminWritingController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomPageController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VisualsController;
use App\Http\Controllers\WritingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/health', HealthController::class)->name('health');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/yazilar', [WritingController::class, 'index'])->name('writing.index');
Route::get('/yazilar/{slug}', [WritingController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('writing.show');

Route::get('/hakkimda', AboutController::class)->name('about');

Route::get('/goruntu', [VisualsController::class, 'index'])->name('visuals.index');
Route::get('/goruntu/{slug}', [VisualsController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('visuals.show');

Route::get('/iletisim', [ContactController::class, 'show'])->name('contact');
Route::post('/iletisim', [ContactController::class, 'send'])
    ->middleware('throttle:3,10')
    ->name('contact.send');

// Legal pages (DB-backed via Page model + template=legal)
Route::get('/hukuksal/{slug}', [LegalPageController::class, 'show'])
    ->where('slug', 'kvkk|gizlilik|kunye')
    ->name('legal.show');
Route::redirect('/kvkk', '/hukuksal/kvkk', 301)->name('legal.kvkk');

// Custom admin-created pages → /sayfa/{slug}.
// Restrictive slug regex avoids collision with named routes (admin/yazilar/...).
Route::get('/sayfa/{slug}', [CustomPageController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('page.show');

// Friendly redirect — /admin/login → /login (Fortify default entry)
Route::redirect('/admin/login', '/login', 302)->name('admin.login');

Route::middleware(['auth', 'ensure.2fa'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/profile/sessions', [SessionsController::class, 'index'])->name('sessions.index');
        Route::delete('/profile/sessions/{device}', [SessionsController::class, 'destroy'])->name('sessions.destroy');

        Route::get('/audit-log', AuditLogController::class)
            ->middleware('role:super-admin|admin')
            ->name('audit-log');

        Route::resource('writings', AdminWritingController::class)
            ->parameters(['writings' => 'writing']);
        Route::post('/writings/{writing}/publish', [AdminWritingController::class, 'publish'])->name('writings.publish');
        Route::post('/writings/{writing}/unpublish', [AdminWritingController::class, 'unpublish'])->name('writings.unpublish');
        Route::post('/writings/{writing}/cover', [AdminWritingController::class, 'uploadCover'])->name('writings.cover.upload');
        Route::delete('/writings/{writing}/cover', [AdminWritingController::class, 'removeCover'])->name('writings.cover.remove');

        Route::resource('publications', AdminPublicationController::class)
            ->parameters(['publications' => 'publication'])
            ->except(['show']);

        Route::resource('pages', AdminPageController::class);

        Route::resource('photos', AdminPhotoController::class)->except(['show']);
        Route::post('/photos/{photo}/publish', [AdminPhotoController::class, 'publish'])->name('photos.publish');
        Route::post('/photos/{photo}/unpublish', [AdminPhotoController::class, 'unpublish'])->name('photos.unpublish');
        Route::delete('/photos/{photo}/image', [AdminPhotoController::class, 'removeImage'])->name('photos.image.remove');

        Route::get('/contact', [ContactMessagesController::class, 'index'])->name('contact.index');
        Route::get('/contact/{contactMessage}', [ContactMessagesController::class, 'show'])->name('contact.show');
        Route::patch('/contact/{contactMessage}', [ContactMessagesController::class, 'update'])->name('contact.update');
        Route::delete('/contact/{contactMessage}', [ContactMessagesController::class, 'destroy'])->name('contact.destroy');

        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/{group}', [AdminSettingsController::class, 'edit'])
            ->where('group', '[a-z_-]+')
            ->name('settings.edit');
        Route::put('/settings/{group}', [AdminSettingsController::class, 'update'])
            ->where('group', '[a-z_-]+')
            ->name('settings.update');

        Route::resource('users', AdminUserController::class)
            ->parameters(['users' => 'user']);

        Route::middleware('can:manage-backups')->prefix('backup')->as('backup.')->group(function (): void {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/', [BackupController::class, 'create'])->name('store');
            Route::get('/download/{filename}', [BackupController::class, 'download'])
                ->where('filename', '[A-Za-z0-9._\-]+\.zip')
                ->name('download');
            Route::delete('/{filename}', [BackupController::class, 'destroy'])
                ->where('filename', '[A-Za-z0-9._\-]+\.zip')
                ->name('destroy');
        });

        Route::post('/photos/bulk-upload', [AdminPhotoController::class, 'bulkUpload'])->name('photos.bulk-upload');
        Route::post('/writings/bulk', WritingBulkController::class)->name('writings.bulk');
        Route::post('/photos/bulk', PhotoBulkController::class)->name('photos.bulk');
    });

Route::middleware('auth')
    ->prefix('admin/two-factor')
    ->as('admin.two-factor.')
    ->group(function (): void {
        Route::get('/', [TwoFactorController::class, 'setup'])->name('setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
        Route::post('/confirm', [TwoFactorController::class, 'confirm'])->name('confirm');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
        Route::post('/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('recovery-codes');
    });

Route::post('/admin/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');
