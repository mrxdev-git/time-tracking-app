<?php

declare(strict_types=1);

namespace App\Domain\Timers\Exceptions;

use App\Domain\Employees\Models\Employee;
use App\Domain\Shared\DomainRuleViolation;
use Throwable;

final class TimerAlreadyRunning extends DomainRuleViolation
{
    public static function for(Employee $employee, Throwable $previous): self
    {
        return new self("A timer is already running for $employee->name.", 0, $previous);
    }
}
