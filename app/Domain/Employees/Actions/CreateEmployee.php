<?php

declare(strict_types=1);

namespace App\Domain\Employees\Actions;

use App\Domain\Employees\Exceptions\EmployeeNameTaken;
use App\Domain\Employees\Models\Employee;
use Illuminate\Database\UniqueConstraintViolationException;

final class CreateEmployee
{
    public function execute(string $name): Employee
    {
        try {
            return Employee::query()->create(['name' => $name]);
        } catch (UniqueConstraintViolationException $exception) {
            throw EmployeeNameTaken::named($name, $exception);
        }
    }
}
