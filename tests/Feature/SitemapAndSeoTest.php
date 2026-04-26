<?php

declare(strict_types=1);

it('serves sitemap.xml with valid XML and absolute URLs', function () {
    $response = $this->get('/sitemap.xml');
    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
    $body = $response->getContent();

    expect($body)->toStartWith('<?xml version="1.0"');
    expect($body)->toContain('<urlset');
    expect($body)->toContain('<loc>');
    // Every <loc> should be absolute
    preg_match_all('#<loc>([^<]+)</loc>#', $body, $matches);
    foreach ($matches[1] as $loc) {
        expect($loc)->toStartWith('http');
    }
});

it('exposes Open Graph + Twitter Card meta on the homepage', function () {
    $response = $this->get('/');
    $response->assertOk();
    $body = $response->getContent();

    expect($body)
        ->toContain('property="og:type"')
        ->toContain('property="og:title"')
        ->toContain('property="og:description"')
        ->toContain('property="og:url"')
        ->toContain('property="og:site_name"')
        ->toContain('name="twitter:card"')
        ->toContain('rel="canonical"');
});

it('includes the sitemap directive in robots.txt', function () {
    $body = (string) file_get_contents(public_path('robots.txt'));
    expect($body)->toContain('Sitemap:');
});

it('returns enriched health JSON with all checks ok', function () {
    $response = $this->get('/health');
    $response->assertOk();
    $response->assertJsonStructure([
        'app',
        'environment',
        'version',
        'time',
        'checks' => [
            'db' => ['status'],
            'cache' => ['status'],
            'storage' => ['status'],
            'media' => ['status'],
        ],
    ]);
    $json = $response->json();
    expect($json['app'])->toBe('ok');
    foreach (['db', 'cache', 'storage', 'media'] as $k) {
        expect($json['checks'][$k]['status'])->toBe('ok');
    }
});
