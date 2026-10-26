<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Duration;
use App\Domain\Timers\Models\TimeEntry;
use App\Support\Clock\Clock;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

final readonly class PeriodReportQuery
{
    public function __construct(private Clock $clock) {}

    public function run(Period $period): PeriodReport
    {
        $generatedAt = $this->clock->now();

        $employees = Employee::query()
            ->with(['timeEntries' => fn (HasMany $entries): HasMany => $entries
                ->chaperone('employee')
                ->where('started_at', '>=', $period->start())
                ->where('started_at', '<', $period->end())
                ->orderBy('started_at')
                ->orderBy('id')])
            ->orderBy('name')
            ->get();

        return new PeriodReport(
            period: $period,
            generatedAt: $generatedAt,
            totals: $employees
                ->map(fn (Employee $employee): EmployeeTotal => $this->totalFor($employee, $generatedAt))
                ->all(),
            entries: $employees->flatMap(fn (Employee $employee): Collection => $employee->timeEntries),
        );
    }

    private function totalFor(Employee $employee, CarbonImmutable $now): EmployeeTotal
    {
        return new EmployeeTotal(
            employee: $employee,
            entryCount: $employee->timeEntries->count(),
            total: $employee->timeEntries->reduce(
                fn (Duration $total, TimeEntry $entry): Duration => $total->add($entry->duration($now)),
                Duration::fromSeconds(0),
            ),
        );
    }
}
