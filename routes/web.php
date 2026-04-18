<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SessionsController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\WritingController as AdminWritingController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WritingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/health', HealthController::class)->name('health');

Route::get('/yazilar', [WritingController::class, 'index'])->name('writing.index');
Route::get('/yazilar/{slug}', [WritingController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('writing.show');

Route::get('/hakkimda', AboutController::class)->name('about');

Route::get('/iletisim', [ContactController::class, 'show'])->name('contact');
Route::post('/iletisim', [ContactController::class, 'send'])
    ->middleware('throttle:3,10')
    ->name('contact.send');

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
