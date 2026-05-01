<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// KVKK m.7 — drop contact messages past their retention window. Daily 03:30
// in Europe/Istanbul (see config/app.php timezone). Idempotent; no-op if empty.
Schedule::command('contact:prune-expired')
    ->dailyAt('03:30')
    ->onOneServer()
    ->withoutOverlapping();

// Backup retention sweep — drops zips older than BACKUP_KEEP_LATEST (14 days).
Schedule::command('backup:clean')
    ->dailyAt('01:55')
    ->onOneServer()
    ->withoutOverlapping();

// Daily DB-only backup — 02:00 Istanbul (low traffic window).
// Lightweight (~150 KB) so we can keep many points-in-time without disk pain.
Schedule::command('backup:run --only-db')
    ->dailyAt('02:00')
    ->onOneServer()
    ->withoutOverlapping();

// Weekly full backup (DB + .env + media when BACKUP_INCLUDE_MEDIA=true).
// Sunday 03:00. Separates from the daily cycle so we don't double-backup.
Schedule::command('backup:run')
    ->weeklyOn(0, '03:00')
    ->onOneServer()
    ->withoutOverlapping();

// Spatie health monitor — alerts on stale / missing / oversized backups
// via mail (config/backup.php > notifications). Daily 04:00.
Schedule::command('backup:monitor')
    ->dailyAt('04:00')
    ->onOneServer();
