<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Models\TimeEntry;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->clock = freezeClock('2026-10-20 09:00:00');
    $this->from(route('employees.index'));
});

it('starts one running entry at the current time with the trimmed description', function (string $submitted, string $stored): void {
    $employee = Employee::factory()->create(['name' => 'Alex Demo']);

    $this->post(route('timer.start', $employee), ['description' => $submitted])
        ->assertRedirect(route('employees.index'))
        ->assertSessionHas('status', 'Timer started for Alex Demo.');

    $this->assertDatabaseCount('time_entries', 1);
    $this->assertDatabaseHas('time_entries', [
        'employee_id' => $employee->id,
        'description' => $stored,
        'started_at' => '2026-10-20 09:00:00',
        'stopped_at' => null,
    ]);
})->with([
    'surrounding whitespace trimmed' => ['  Review  ', 'Review'],
    'exactly 255 characters' => [str_repeat('a', 255), str_repeat('a', 255)],
]);

it('shows the running timer with its description, a stop form and the elapsed time after a reload', function (): void {
    $employee = Employee::factory()->create(['name' => 'Alex Demo']);
    $this->post(route('timer.start', $employee), ['description' => 'Review']);
    $this->clock->advance('1 hour 2 minutes 3 seconds');

    $this->get(route('employees.index'))
        ->assertOk()
        ->assertSee('Review')
        ->assertSee(route('timer.stop', $employee))
        ->assertDontSee(route('timer.start', $employee))
        ->assertSee('data-started-at="2026-10-20T09:00:00Z"', false)
        ->assertSee('data-server-now="2026-10-20T10:02:03Z"', false)
        ->assertSee('1:02:03');
});

it('rejects a second start while a timer is running and keeps the single running entry', function (): void {
    $employee = Employee::factory()->create(['name' => 'Alex Demo']);
    $this->post(route('timer.start', $employee), ['description' => 'Review']);
    $this->clock->advance('10 minutes');

    $this->post(route('timer.start', $employee), ['description' => 'Deploy'])
        ->assertRedirect(route('employees.index'));

    $this->assertDatabaseCount('time_entries', 1);
    $this->assertDatabaseHas('time_entries', [
        'employee_id' => $employee->id,
        'description' => 'Review',
        'started_at' => '2026-10-20 09:00:00',
        'stopped_at' => null,
    ]);

    $this->get(route('employees.index'))->assertSee('A timer is already running for Alex Demo.');
});

it('refuses a second running entry for the same employee in the database itself', function (): void {
    $employee = Employee::factory()->create();
    TimeEntry::factory()->running()->for($employee)->create();

    expect(fn (): bool => DB::table('time_entries')->insert([
        'employee_id' => $employee->id,
        'description' => 'Raw insert',
        'started_at' => '2026-10-20 09:30:00',
        'stopped_at' => null,
    ]))->toThrow(UniqueConstraintViolationException::class);

    expect($employee->timeEntries()->whereNull('stopped_at')->count())->toBe(1);
});

it('lets different employees run timers at the same time', function (): void {
    $alex = Employee::factory()->create(['name' => 'Alex Demo']);
    $jordan = Employee::factory()->create(['name' => 'Jordan Sample']);

    $this->post(route('timer.start', $alex), ['description' => 'Review'])->assertSessionHasNoErrors();
    $this->post(route('timer.start', $jordan), ['description' => 'Deploy'])->assertSessionHasNoErrors();

    expect(TimeEntry::query()->whereNull('stopped_at')->pluck('employee_id')->all())
        ->toEqualCanonicalizing([$alex->id, $jordan->id]);
});

it('rejects an invalid description with a visible message and starts no timer', function (string $description, string $message): void {
    $employee = Employee::factory()->create();

    $this->post(route('timer.start', $employee), ['description' => $description])
        ->assertRedirect(route('employees.index'));

    $this->assertDatabaseEmpty('time_entries');

    $this->get(route('employees.index'))->assertSee($message);
})->with([
    'empty' => ['', 'The description field is required.'],
    'whitespace only' => ['   ', 'The description field is required.'],
    'longer than 255 characters' => [str_repeat('a', 256), 'The description field must not be greater than 255 characters.'],
]);

it('responds 404 when starting a timer for an unknown employee', function (): void {
    $this->post(route('timer.start', ['employee' => 999]), ['description' => 'Review'])
        ->assertNotFound();

    $this->assertDatabaseEmpty('time_entries');
});
