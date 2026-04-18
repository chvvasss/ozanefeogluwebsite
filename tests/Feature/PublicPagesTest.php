<?php

declare(strict_types=1);

it('renders the landing page for anonymous visitors', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Builds', false);
});

it('returns a useful 404 for unknown routes', function () {
    $response = $this->get('/non-existent-route-xyz');

    $response->assertNotFound();
});
