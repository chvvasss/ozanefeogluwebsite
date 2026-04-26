<?php

namespace Tests;

use App\Support\SettingsRepository;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // SettingsRepository keeps a static in-memory cache that otherwise
        // leaks across tests (they share the PHP process). Flush on each
        // boot so config() overrides + fresh DB rows are seen correctly.
        SettingsRepository::flush();
    }
}
