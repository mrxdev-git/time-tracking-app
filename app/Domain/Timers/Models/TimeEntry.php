<?php

declare(strict_types=1);

namespace App\Domain\Timers\Models;

use App\Domain\Employees\Models\Employee;
use App\Domain\Timers\Duration;
use Carbon\CarbonImmutable;
use Database\Factories\TimeEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['description', 'started_at'])]
#[UseFactory(TimeEntryFactory::class)]
class TimeEntry extends Model
{
    use HasFactory;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function isRunning(): bool
    {
        return $this->stopped_at === null;
    }

    public function duration(CarbonImmutable $now): Duration
    {
        return Duration::between($this->started_at, $this->stopped_at ?? $now);
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'immutable_datetime',
            'stopped_at' => 'immutable_datetime',
        ];
    }
}
