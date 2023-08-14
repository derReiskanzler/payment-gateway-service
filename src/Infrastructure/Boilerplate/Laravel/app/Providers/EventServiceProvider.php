<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Events\ProophEventStoredInStream;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Listeners\LaravelConsumeProjectorProophEventStoredListener;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Listeners\LaravelProduceProjectorProophEventStoredListener;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Listeners\TraceIdResponseEnricher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<class-string>>
     */
    protected $listen = [
        RequestHandled::class => [
            TraceIdResponseEnricher::class,
        ],
        ProophEventStoredInStream::class => [
            LaravelProduceProjectorProophEventStoredListener::class,
            LaravelConsumeProjectorProophEventStoredListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array<class-string>
     */
    protected $subscribe = [
    ];
}
