<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Duration;

final readonly class EmployeeTotal
{
    public function __construct(
        public Employee $employee,
        public int $entryCount,
        public Duration $total,
    ) {}
}
