<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Models\TimeEntry;

it('shows the empty state and every employee at zero when the period has no entries', function (): void {
    Employee::factory()->create(['name' => 'Jordan Sample']);
    TimeEntry::factory()
        ->for(Employee::factory()->create(['name' => 'Alex Demo']))
        ->create(['description' => 'Earlier task', 'started_at' => '2026-10-01 09:00:00', 'stopped_at' => '2026-10-01 10:00:00']);

    $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-21']))
        ->assertOk()
        ->assertSeeText('No time entries between 2026-10-20 and 2026-10-21.')
        ->assertSeeTextInOrder(['Alex Demo', '0:00:00', 'Jordan Sample', '0:00:00'])
        ->assertDontSeeText('Earlier task')
        ->assertDontSee('role="alert"', false);
});

it('says there are no employees yet when nobody has been added', function (): void {
    $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-21']))
        ->assertOk()
        ->assertSeeText('No employees yet.')
        ->assertSeeText('No time entries between 2026-10-20 and 2026-10-21.')
        ->assertDontSee('role="alert"', false);
});
