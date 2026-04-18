<?php

declare(strict_types=1);

use App\Services\HibpService;
use Illuminate\Support\Facades\Http;

it('detects a known pwned password suffix', function () {
    $password = 'hunter2';
    $sha1 = strtoupper(sha1($password));
    $prefix = substr($sha1, 0, 5);
    $suffix = substr($sha1, 5);

    Http::fake([
        "*/range/{$prefix}" => Http::response("{$suffix}:12345\nABCDEF:1", 200),
    ]);

    $service = new HibpService(
        baseUrl: 'https://api.pwnedpasswords.com',
        timeout: 3,
        cacheTtl: 0,
        enabled: true,
    );

    expect($service->occurrencesOf($password))->toBe(12345)
        ->and($service->isCompromised($password))->toBeTrue();
});

it('returns zero when password not found', function () {
    Http::fake([
        '*' => Http::response("FOOBAR:1\nBAZ:2", 200),
    ]);

    $service = new HibpService(
        baseUrl: 'https://api.pwnedpasswords.com',
        timeout: 3,
        cacheTtl: 0,
        enabled: true,
    );

    expect($service->occurrencesOf('definitely-not-pwned-xyz-42'))->toBe(0);
});

it('is a no-op when disabled', function () {
    $service = new HibpService(
        baseUrl: 'https://example.invalid',
        timeout: 1,
        cacheTtl: 0,
        enabled: false,
    );

    expect($service->occurrencesOf('anything'))->toBe(0);
});

it('tolerates network failures gracefully', function () {
    Http::fake(fn () => Http::response('', 500));

    $service = new HibpService(
        baseUrl: 'https://example.invalid',
        timeout: 1,
        cacheTtl: 0,
        enabled: true,
    );

    expect($service->occurrencesOf('anything'))->toBe(0);
});
