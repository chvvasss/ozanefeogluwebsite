<?php

declare(strict_types=1);

it('returns ok on the health endpoint', function () {
    $response = $this->getJson('/health');

    $response->assertOk();
    $response->assertJson(['app' => 'ok']);
    $response->assertJsonPath('checks.db.status', 'ok');
    $response->assertJsonPath('checks.cache.status', 'ok');
    $response->assertJsonPath('checks.storage.status', 'ok');
});

it('exposes X-Frame-Options DENY on every response', function () {
    $response = $this->get('/');
    expect($response->headers->get('X-Frame-Options'))->toBe('DENY');
});

it('echoes a request id header', function () {
    $response = $this->get('/health');

    expect($response->headers->get('X-Request-Id'))->not->toBeEmpty();
});

it('sets security headers', function () {
    $response = $this->get('/');

    expect($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin')
        ->and($response->headers->get('Cross-Origin-Opener-Policy'))->toBe('same-origin');
});
