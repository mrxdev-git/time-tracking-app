<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Employees\Models\Employee;
use App\Support\Clock\Clock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const array SCHEDULE = [
        'Alex Demo' => [
            ['Sprint planning', 1, '09:00', '10:30'],
            ['Code review', 3, '13:15', '15:05'],
        ],
        'Jordan Sample' => [
            ['Customer support', 2, '08:30', '12:00'],
            ['Release notes', 5, '14:00', '15:45'],
        ],
        'Taylor Example' => [
            ['Design workshop', 4, '10:00', '12:20'],
            ['Bug triage', 6, '16:00', '16:50'],
        ],
    ];

    public function run(Clock $clock): void
    {
        $today = $clock->now()->startOfDay();

        foreach (self::SCHEDULE as $name => $entries) {
            $employee = Employee::query()->firstOrCreate(['name' => $name]);

            if (! $employee->wasRecentlyCreated) {
                continue;
            }

            foreach ($entries as [$description, $daysAgo, $startTime, $stopTime]) {
                $day = $today->subDays($daysAgo);

                $employee->timeEntries()->forceCreate([
                    'description' => $description,
                    'started_at' => $day->setTimeFromTimeString($startTime),
                    'stopped_at' => $day->setTimeFromTimeString($stopTime),
                ]);
            }
        }
    }
}
