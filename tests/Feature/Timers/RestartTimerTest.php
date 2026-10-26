<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;
use App\Domain\Reports\PeriodReport;
use App\Domain\Timers\Models\TimeEntry;

beforeEach(function (): void {
    $this->clock = freezeClock('2026-10-20 09:00:00');
});

it('records a restarted timer as a new entry and totals both entries in the report', function (): void {
    $employee = Employee::factory()->create(['name' => 'Alex Demo']);

    $this->post(route('timer.start', $employee), ['description' => 'Review']);
    $this->clock->advance('30 minutes');
    $this->post(route('timer.stop', $employee));
    $this->clock->advance('2 hours');
    $this->post(route('timer.start', $employee), ['description' => 'Review']);
    $this->clock->advance('1 hour 10 minutes');
    $this->post(route('timer.stop', $employee));

    $entries = $employee->timeEntries()->orderBy('id')->get();

    expect($entries)->toHaveCount(2)
        ->and($entries->pluck('id')->unique())->toHaveCount(2)
        ->and($entries->map(fn (TimeEntry $entry): array => [
            $entry->started_at->toDateTimeString(),
            $entry->stopped_at?->toDateTimeString(),
        ])->all())->toBe([
            ['2026-10-20 09:00:00', '2026-10-20 09:30:00'],
            ['2026-10-20 11:30:00', '2026-10-20 12:40:00'],
        ])
        ->and($entries->sum(fn (TimeEntry $entry): int => $entry->duration($this->clock->now())->seconds))->toBe(6000);

    $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-20']))
        ->assertOk()
        ->assertViewHas('report', fn (PeriodReport $report): bool => $report->entries->count() === 2
            && $report->totals[0]->entryCount === 2
            && $report->totals[0]->total->seconds === 6000)
        ->assertSee('1:40:00');
});
