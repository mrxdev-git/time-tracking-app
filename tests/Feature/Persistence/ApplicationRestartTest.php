<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Models\TimeEntry;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

function useSqliteFile(string $path): void
{
    config(['database.connections.sqlite.database' => $path]);
    DB::purge('sqlite');
}

beforeEach(function (): void {
    $this->databaseFile = tempnam(sys_get_temp_dir(), 'time-tracker-');
});

afterEach(function (): void {
    DB::disconnect();
    unlink($this->databaseFile);
});

it('keeps tracked time after the application restarts', function (): void {
    useSqliteFile($this->databaseFile);
    expect(Artisan::call('migrate', ['--force' => true]))->toBe(0);
    $clock = freezeClock('2026-10-20 09:00:00');

    $this->post(route('employees.store'), ['name' => 'Alex Demo'])->assertSessionHasNoErrors();
    $employee = Employee::query()->sole();
    $this->post(route('timer.start', $employee), ['description' => 'Persisted task'])->assertSessionHasNoErrors();
    $clock->advance('25 minutes');
    $this->post(route('timer.stop', $employee))->assertSessionHasNoErrors();

    DB::disconnect();
    $stoppedApplication = $this->app;
    $this->refreshApplication();

    expect($this->app)->not->toBe($stoppedApplication)
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:');

    useSqliteFile($this->databaseFile);

    $this->get(route('report.show', ['from' => '2026-10-20', 'to' => '2026-10-20']))
        ->assertOk()
        ->assertSeeText('Alex Demo')
        ->assertSeeText('Persisted task')
        ->assertSeeText('0:25:00');

    $entry = TimeEntry::query()->sole();
    expect($entry->description)->toBe('Persisted task')
        ->and($entry->started_at->toDateTimeString())->toBe('2026-10-20 09:00:00')
        ->and($entry->stopped_at->toDateTimeString())->toBe('2026-10-20 09:25:00');
});
