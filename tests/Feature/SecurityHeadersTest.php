<?php

declare(strict_types=1);

it('sets the baseline security headers on every response', function () {
    $response = $this->get('/');

    expect($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin')
        ->and($response->headers->get('Cross-Origin-Opener-Policy'))->toBe('same-origin')
        ->and($response->headers->get('Cross-Origin-Resource-Policy'))->toBe('same-origin');
});

it('locks down the permissions policy', function () {
    $response = $this->get('/');

    $permissions = (string) $response->headers->get('Permissions-Policy');
    expect($permissions)->toContain('camera=()')
        ->and($permissions)->toContain('microphone=()')
        ->and($permissions)->toContain('geolocation=()')
        ->and($permissions)->toContain('interest-cohort=()');
});

it('issues a request id on every response', function () {
    $response = $this->get('/');

    $id = (string) $response->headers->get('X-Request-Id');
    expect($id)->not->toBeEmpty()->toMatch('/^[a-zA-Z0-9\-]{8,64}$/');
});

it('does not emit HSTS in non-production environments', function () {
    expect(app()->environment('production'))->toBeFalse();

    $response = $this->get('/');
    expect($response->headers->has('Strict-Transport-Security'))->toBeFalse();
});

it('does not emit CSP in local environment (disabled by default)', function () {
    config(['security.csp.enabled' => false]);
    $response = $this->get('/');
    expect($response->headers->has('Content-Security-Policy'))->toBeFalse();
});

it('emits a strict CSP with a per-request nonce when enabled', function () {
    config(['security.csp.enabled' => true]);
    $response = $this->get('/');
    $csp = (string) $response->headers->get('Content-Security-Policy');

    expect($csp)->not->toBeEmpty()
        ->and($csp)->toContain("default-src 'self'")
        ->and($csp)->toContain("frame-ancestors 'none'")
        ->and($csp)->toContain("object-src 'none'")
        ->and($csp)->toContain('upgrade-insecure-requests')
        ->and($csp)->toMatch('/script-src \'self\' \'nonce-[a-f0-9]{32}\'/');
});

it('rejects an unsafe inbound request id and issues its own', function () {
    $response = $this->withHeaders(['X-Request-Id' => "malicious\nheader;"])->get('/');
    $id = (string) $response->headers->get('X-Request-Id');

    expect($id)->not->toContain("\n")
        ->and($id)->not->toContain(';')
        ->and($id)->toMatch('/^[a-zA-Z0-9\-]{8,64}$/');
});
