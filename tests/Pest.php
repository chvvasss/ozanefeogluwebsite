<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extends(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extends(TestCase::class)
    ->in('Unit');
