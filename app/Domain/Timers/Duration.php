<?php

declare(strict_types=1);

namespace App\Domain\Timers;

use Carbon\CarbonImmutable;

final readonly class Duration
{
    private function __construct(public int $seconds) {}

    public static function fromSeconds(int $seconds): self
    {
        return new self($seconds);
    }

    public static function between(CarbonImmutable $start, CarbonImmutable $end): self
    {
        return new self($end->getTimestamp() - $start->getTimestamp());
    }

    public function add(Duration $other): Duration
    {
        return new self($this->seconds + $other->seconds);
    }

    public function format(): string
    {
        $hours = intdiv($this->seconds, 3600);
        $minutes = intdiv($this->seconds % 3600, 60);
        $seconds = $this->seconds % 60;

        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
