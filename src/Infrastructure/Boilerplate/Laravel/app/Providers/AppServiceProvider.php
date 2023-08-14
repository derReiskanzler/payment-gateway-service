<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProvider;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->singleton(TraceIdProviderInterface::class, static fn () => new TraceIdProvider());
    }
}
