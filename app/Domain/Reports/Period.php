<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use Carbon\CarbonImmutable;

final readonly class Period
{
    private function __construct(
        public CarbonImmutable $from,
        public CarbonImmutable $to,
    ) {}

    public static function fromDates(string $from, string $to): self
    {
        return new self(self::date($from), self::date($to));
    }

    public function start(): CarbonImmutable
    {
        return $this->from;
    }

    public function end(): CarbonImmutable
    {
        return $this->to->addDay();
    }

    private static function date(string $value): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('!Y-m-d', $value, 'UTC');
    }
}
