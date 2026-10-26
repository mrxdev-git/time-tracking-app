<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Clock\Clock;
use App\Support\Clock\SystemClock;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Clock::class, SystemClock::class);
    }
}
