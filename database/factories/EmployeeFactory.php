<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Employees\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
        ];
    }
}
