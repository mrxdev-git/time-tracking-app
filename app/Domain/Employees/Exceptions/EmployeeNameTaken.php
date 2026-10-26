<?php

declare(strict_types=1);

namespace App\Domain\Employees\Exceptions;

use App\Domain\Shared\DomainRuleViolation;
use Throwable;

final class EmployeeNameTaken extends DomainRuleViolation
{
    public static function named(string $name, Throwable $previous): self
    {
        return new self("An employee named \"$name\" already exists.", 0, $previous);
    }
}
