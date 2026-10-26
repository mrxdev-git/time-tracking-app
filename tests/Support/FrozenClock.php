<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Support\Clock\Clock;
use Carbon\CarbonImmutable;
use DateInterval;

final class FrozenClock implements Clock
{
    public function __construct(private CarbonImmutable $now) {}

    public function now(): CarbonImmutable
    {
        return $this->now;
    }

    public function advance(string|DateInterval $by): void
    {
        $this->now = $this->now->add($by);
    }

    public function set(CarbonImmutable $now): void
    {
        $this->now = $now;
    }
}
