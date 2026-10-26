<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Models\TimeEntry;
use App\Support\Clock\Clock;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'description' => fake()->sentence(3),
            'started_at' => app(Clock::class)->now()->subHours(2),
            'stopped_at' => fn (array $attributes): CarbonImmutable => CarbonImmutable::parse($attributes['started_at'], 'UTC')->addHour(),
        ];
    }

    public function running(): static
    {
        return $this->state(['stopped_at' => null]);
    }
}
