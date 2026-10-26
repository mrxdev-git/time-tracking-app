<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Models\TimeEntry;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->clock = freezeClock('2026-10-20 09:00:00');
    $this->from(route('employees.index'));
});

it('stops the running entry at the current time and leaves other timers running', function (): void {
    $employee = Employee::factory()->create(['name' => 'Alex Demo']);
    $entry = TimeEntry::factory()->running()->for($employee)->create(['started_at' => $this->clock->now()]);
    $otherEntry = TimeEntry::factory()->running()->create();
    $this->clock->advance('1 hour 30 minutes');

    $this->post(route('timer.stop', $employee))
        ->assertRedirect(route('employees.index'))
        ->assertSessionHas('status', 'Timer stopped for Alex Demo.');

    expect($entry->refresh()->stopped_at)->toEqual($this->clock->now())
        ->and($entry->duration($this->clock->now())->format())->toBe('1:30:00')
        ->and($otherEntry->refresh()->isRunning())->toBeTrue();
});

it('rejects stopping when no timer is running and changes no rows', function (): void {
    $employee = Employee::factory()->create(['name' => 'Alex Demo']);
    TimeEntry::factory()->for($employee)->create();
    TimeEntry::factory()->running()->create();
    $rowsBefore = DB::table('time_entries')->orderBy('id')->get();

    $this->post(route('timer.stop', $employee))
        ->assertRedirect(route('employees.index'));

    expect(DB::table('time_entries')->orderBy('id')->get())->toEqual($rowsBefore);

    $this->get(route('employees.index'))->assertSee('No timer is running for Alex Demo.');
});

it('responds 404 when stopping a timer for an unknown employee', function (): void {
    $this->post(route('timer.stop', ['employee' => 999]))->assertNotFound();
});
