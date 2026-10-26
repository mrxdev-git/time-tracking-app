<?php

declare(strict_types=1);

use App\Support\Clock\Clock;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Util\ExcludeList;
use Tests\Support\FrozenClock;
use Tests\TestCase;

$projectRoot = dirname(__DIR__);

ExcludeList::addDirectory("$projectRoot/vendor/vlucas/phpdotenv");

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature/Employees', 'Feature/Timers', 'Feature/Reports');

pest()->extend(TestCase::class)
    ->in('Feature/Persistence');

function freezeClock(string $utc = '2026-10-20 09:00:00'): FrozenClock
{
    $clock = new FrozenClock(CarbonImmutable::parse($utc, 'UTC'));

    app()->instance(Clock::class, $clock);

    return $clock;
}
