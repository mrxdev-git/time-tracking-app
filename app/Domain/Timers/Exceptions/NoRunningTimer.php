<?php

declare(strict_types=1);

namespace App\Domain\Timers\Exceptions;

use App\Domain\Employees\Models\Employee;
use App\Domain\Shared\DomainRuleViolation;

final class NoRunningTimer extends DomainRuleViolation
{
    public static function for(Employee $employee): self
    {
        return new self("No timer is running for $employee->name.");
    }
}
