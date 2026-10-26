<?php

declare(strict_types=1);

namespace App\Domain\Employees\Models;

use App\Domain\Timers\Models\TimeEntry;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name'])]
#[UseFactory(EmployeeFactory::class)]
class Employee extends Model
{
    use HasFactory;

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function runningEntry(): HasOne
    {
        return $this->hasOne(TimeEntry::class)->whereNull('stopped_at');
    }
}
