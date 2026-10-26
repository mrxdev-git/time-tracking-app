<?php

declare(strict_types=1);

namespace App\Domain\Timers\Actions;

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Exceptions\TimerAlreadyRunning;
use App\Domain\Timers\Models\TimeEntry;
use App\Support\Clock\Clock;
use Illuminate\Database\UniqueConstraintViolationException;

final readonly class StartTimer
{
    public function __construct(private Clock $clock) {}

    public function execute(Employee $employee, string $description): TimeEntry
    {
        try {
            return $employee->timeEntries()->create([
                'description' => $description,
                'started_at' => $this->clock->now(),
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            throw TimerAlreadyRunning::for($employee, $exception);
        }
    }
}
