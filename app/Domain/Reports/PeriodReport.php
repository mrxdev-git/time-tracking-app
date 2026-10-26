<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final readonly class PeriodReport
{
    public function __construct(
        public Period $period,
        public CarbonImmutable $generatedAt,
        public array $totals,
        public Collection $entries,
    ) {}

    public function isEmpty(): bool
    {
        return $this->entries->isEmpty();
    }
}
