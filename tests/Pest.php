<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Util\ExcludeList;
use Tests\TestCase;

$projectRoot = dirname(__DIR__);

ExcludeList::addDirectory("$projectRoot/vendor/vlucas/phpdotenv");

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature/Employees', 'Feature/Timers', 'Feature/Reports');

pest()->extend(TestCase::class)
    ->in('Feature/Persistence');
