<?php

declare(strict_types=1);

namespace App\Domain\Timers\Actions;

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Exceptions\NoRunningTimer;
use App\Domain\Timers\Models\TimeEntry;
use App\Support\Clock\Clock;

final readonly class StopTimer
{
    public function __construct(private Clock $clock) {}

    public function execute(Employee $employee): TimeEntry
    {
        $now = $this->clock->now();

        $stopped = $employee->runningEntry()->update(['stopped_at' => $now]);

        if ($stopped === 0) {
            throw NoRunningTimer::for($employee);
        }

        return $employee->timeEntries()
            ->where('stopped_at', $now)
            ->latest('id')
            ->firstOrFail();
    }
}
