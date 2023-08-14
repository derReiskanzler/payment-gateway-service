<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Console;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\EventProjections\Services\Configurations\LaravelProjectionConfigurationProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, string>
     */
    protected $commands = [
        Commands\RetryFailedProjections::class,
    ];

    /**
     * @throws ProjectionConfigurationInvalidException
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->runProducingProjections($schedule);
        $this->runConsumingProjections($schedule);
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands(): void
    {
        /** @noinspection PhpIncludeInspection */
        require app()->routesPath().\DIRECTORY_SEPARATOR.'console.php';
    }

    /**
     * @throws ProjectionConfigurationInvalidException
     */
    private function runProducingProjections(Schedule $schedule): void
    {
        $projectionConfigurationProvider = new LaravelProjectionConfigurationProvider('projections');
        $projectionNames = $projectionConfigurationProvider->getProjectionConfigurationNames(
            LaravelProjectionConfigurationProvider::PRODUCING_PROJECTION_TYPE
        );

        foreach ($projectionNames as $projectionName) {
            $projection = $projectionConfigurationProvider->getProjectionConfiguration(
                $projectionName,
                LaravelProjectionConfigurationProvider::PRODUCING_PROJECTION_TYPE
            );

            if ($projection->getBackgroundProcess()) {
                $schedule->command('projector:producing:start', [$projectionName])
                    ->everyMinute()
                    ->runInBackground();
            }
        }
    }

    /**
     * @param Schedule $schedule Schedule
     *
     * @throws ProjectionConfigurationInvalidException
     */
    private function runConsumingProjections(Schedule $schedule): void
    {
        $projectionConfigurationProvider = new LaravelProjectionConfigurationProvider('projections');
        $projectionNames = $projectionConfigurationProvider->getProjectionConfigurationNames(
            LaravelProjectionConfigurationProvider::CONSUMING_PROJECTION_TYPE
        );
        foreach ($projectionNames as $projectionName) {
            $projection = $projectionConfigurationProvider->getProjectionConfiguration(
                $projectionName,
                LaravelProjectionConfigurationProvider::CONSUMING_PROJECTION_TYPE
            );

            if ($projection->getBackgroundProcess()) {
                $schedule->command('projector:consuming:start', [$projectionName])
                    ->everyMinute()
                    ->runInBackground();
            }
        }
    }
}
