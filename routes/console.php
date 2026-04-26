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
