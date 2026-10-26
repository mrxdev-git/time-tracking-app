<?php

declare(strict_types=1);

namespace App\Http\Controllers\Timers;

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Actions\StartTimer;
use App\Domain\Timers\Actions\StopTimer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Timers\StartTimerRequest;
use Illuminate\Http\RedirectResponse;

final class TimerController extends Controller
{
    public function start(StartTimerRequest $request, Employee $employee, StartTimer $startTimer): RedirectResponse
    {
        $startTimer->execute($employee, $request->taskDescription());

        return to_route('employees.index')->with('status', "Timer started for $employee->name.");
    }

    public function stop(Employee $employee, StopTimer $stopTimer): RedirectResponse
    {
        $stopTimer->execute($employee);

        return to_route('employees.index')->with('status', "Timer stopped for $employee->name.");
    }
}
