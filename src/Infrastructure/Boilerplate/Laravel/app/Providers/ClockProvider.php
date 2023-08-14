<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock\Clock;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock\SystemClock;
use Illuminate\Support\ServiceProvider;

class ClockProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Clock::class, static fn () => new SystemClock());
    }
}
