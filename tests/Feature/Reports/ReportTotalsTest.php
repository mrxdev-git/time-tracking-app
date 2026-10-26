<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;
use App\Domain\Reports\EmployeeTotal;
use App\Domain\Timers\Models\TimeEntry;
use Illuminate\Testing\TestResponse;

function reportedTotals(TestResponse $response): array
{
    return collect($response->viewData('report')->totals)
        ->mapWithKeys(fn (EmployeeTotal $total): array => [
            $total->employee->name => ['entries' => $total->entryCount, 'total' => $total->total->format()],
        ])
        ->all();
}

it('counts an entry started inside the period with its full duration', function (string $startedAt, string $stoppedAt): void {
    freezeClock('2026-10-23 12:00:00');
    TimeEntry::factory()
        ->for(Employee::factory()->create(['name' => 'Alex Demo']))
        ->create(['description' => 'Boundary task', 'started_at' => $startedAt, 'stopped_at' => $stoppedAt]);

    $response = $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-21']));

    $response->assertOk()->assertSeeText('Boundary task');
    expect(reportedTotals($response))->toBe(['Alex Demo' => ['entries' => 1, 'total' => '1:00:00']]);
})->with([
    'started at the first second of from' => ['2026-10-20 00:00:00', '2026-10-20 01:00:00'],
    'started at the last second of to and stopped after the period' => ['2026-10-21 23:59:59', '2026-10-22 00:59:59'],
]);

it('leaves out an entry started outside the period', function (string $startedAt, string $stoppedAt): void {
    freezeClock('2026-10-23 12:00:00');
    TimeEntry::factory()
        ->for(Employee::factory()->create(['name' => 'Alex Demo']))
        ->create(['description' => 'Boundary task', 'started_at' => $startedAt, 'stopped_at' => $stoppedAt]);

    $response = $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-21']));

    $response->assertOk()
        ->assertDontSeeText('Boundary task')
        ->assertSeeText('No time entries between 2026-10-20 and 2026-10-21.');
    expect(reportedTotals($response))->toBe(['Alex Demo' => ['entries' => 0, 'total' => '0:00:00']]);
})->with([
    'started one second before from and stopped inside the period' => ['2026-10-19 23:59:59', '2026-10-20 01:00:00'],
    'started at the first second after to' => ['2026-10-22 00:00:00', '2026-10-22 01:00:00'],
]);

it('counts a running entry up to the generation time and labels it running', function (): void {
    freezeClock('2026-10-21 12:00:00');
    TimeEntry::factory()
        ->running()
        ->for(Employee::factory()->create(['name' => 'Alex Demo']))
        ->create(['description' => 'Code review', 'started_at' => '2026-10-21 09:30:00']);

    $response = $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-21']));

    $response->assertOk()
        ->assertSeeTextInOrder(['From 2026-10-20 to 2026-10-21 (inclusive), generated at', '2026-10-21 12:00:00', 'UTC.'])
        ->assertSeeTextInOrder(['Code review', 'running', '2:30:00']);
    expect(reportedTotals($response))->toBe(['Alex Demo' => ['entries' => 1, 'total' => '2:30:00']]);
});

it('totals every employee in name order, including employees without entries', function (): void {
    freezeClock('2026-10-21 18:00:00');
    Employee::factory()->create(['name' => 'Taylor Example']);
    TimeEntry::factory()
        ->for(Employee::factory()->create(['name' => 'Jordan Sample']))
        ->create(['started_at' => '2026-10-20 13:00:00', 'stopped_at' => '2026-10-20 15:15:00']);
    TimeEntry::factory()
        ->for(Employee::factory()->create(['name' => 'Alex Demo']))
        ->createMany([
            ['started_at' => '2026-10-20 09:00:00', 'stopped_at' => '2026-10-20 10:00:00'],
            ['started_at' => '2026-10-21 14:00:00', 'stopped_at' => '2026-10-21 14:30:00'],
        ]);

    $response = $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-21']));

    $response->assertOk();
    expect(reportedTotals($response))->toBe([
        'Alex Demo' => ['entries' => 2, 'total' => '1:30:00'],
        'Jordan Sample' => ['entries' => 1, 'total' => '2:15:00'],
        'Taylor Example' => ['entries' => 0, 'total' => '0:00:00'],
    ]);
});

it('sums both entries when a timer is stopped and started again', function (): void {
    $clock = freezeClock('2026-10-20 09:00:00');
    $employee = Employee::factory()->create(['name' => 'Alex Demo']);

    $this->post(route('timer.start', $employee), ['description' => 'Review']);
    $clock->advance('30 minutes');
    $this->post(route('timer.stop', $employee));
    $clock->advance('1 hour');
    $this->post(route('timer.start', $employee), ['description' => 'Review']);
    $clock->advance('45 minutes');
    $this->post(route('timer.stop', $employee));

    $response = $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-20']));

    $response->assertOk();
    expect(reportedTotals($response))->toBe(['Alex Demo' => ['entries' => 2, 'total' => '1:15:00']]);
});
