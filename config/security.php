<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | 2FA policy
    |--------------------------------------------------------------------------
    */

    'require_2fa_for_admin' => (bool) env('REQUIRE_2FA_FOR_ADMIN', true),

    /*
    |--------------------------------------------------------------------------
    | Login throttle
    |--------------------------------------------------------------------------
    | Per IP + username attempts; beyond threshold, return 429 with cooldown.
    */

    'login' => [
        'throttle_attempts' => (int) env('AUTH_LOGIN_THROTTLE_ATTEMPTS', 5),
        'throttle_decay_minutes' => 1,
        'lockout_minutes' => (int) env('AUTH_LOGIN_LOCKOUT_MINUTES', 15),
        'daily_lockout_threshold' => (int) env('AUTH_LOGIN_DAILY_LOCKOUT_THRESHOLD', 10),
        // Send a notification email after this many consecutive failures.
        // Must be < daily_lockout_threshold to give the user a heads-up
        // BEFORE the account locks.
        'alert_threshold' => (int) env('AUTH_LOGIN_ALERT_THRESHOLD', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | HIBP Pwned Passwords k-anonymity
    |--------------------------------------------------------------------------
    */

    'hibp' => [
        'enabled' => (bool) env('HIBP_ENABLED', true),
        'base_url' => env('HIBP_API_BASE', 'https://api.pwnedpasswords.com'),
        'timeout' => (int) env('HIBP_TIMEOUT', 3),
        'cache_ttl' => 60 * 60 * 24 * 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Content-Security-Policy
    |--------------------------------------------------------------------------
    */

    'csp' => [
        'enabled' => env('APP_ENV') !== 'local',
        'report_uri' => env('SECURITY_CSP_REPORT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HSTS
    |--------------------------------------------------------------------------
    */

    'hsts' => [
        'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 63072000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    */

    'password' => [
        'min_length' => 12,
        'max_length' => 128,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session hardening
    |--------------------------------------------------------------------------
    */

    'session' => [
        'idle_timeout_minutes' => 30,
        'absolute_timeout_hours' => 8,
    ],
];
