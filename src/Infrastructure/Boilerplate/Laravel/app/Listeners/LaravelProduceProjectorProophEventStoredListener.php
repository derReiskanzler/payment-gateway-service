<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Listeners;

use Allmyhomes\EventProjections\Services\Projections\Trigger\LaravelEventStoredProduceProjector;
use Allmyhomes\EventProjections\Services\Projections\Trigger\ProjectionTriggerDto;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Events\ProophEventStoredInStream;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use PDOException;
use Psr\Log\LoggerInterface;
use Throwable;

class LaravelProduceProjectorProophEventStoredListener implements ShouldQueue
{
    use InteractsWithQueue;

    private const DB_GENERAL_TYPE = 'general_error';
    private const DB_ERROR_TYPE = 'database_error';

    public string $queue = 'domain-events';

    private LaravelEventStoredProduceProjector $laravelEventStoredProduceProjector;

    private LoggerInterface $logger;

    public function __construct(LaravelEventStoredProduceProjector $laravelEventStoredProduceProjector, LoggerInterface $logger)
    {
        $this->laravelEventStoredProduceProjector = $laravelEventStoredProduceProjector;
        $this->logger = $logger;
    }

    public function handle(ProophEventStoredInStream $event): void
    {
        $eventListenerDto = new ProjectionTriggerDto($event->getEventStreamName(), $event->getProophEvent()->messageName());
        $this->laravelEventStoredProduceProjector->handle($eventListenerDto);
    }

    public function failed(ProophEventStoredInStream $event, Throwable $exception): void
    {
        $this->logger->critical(
            'Failed to produce event using queues',
            [
                'error_type' => $this->getErrorType($exception),
                'operation' => 'producing',
                'stream_name' => $event->getEventStreamName(),
                'event_name' => $event->getProophEvent()->messageName(),
            ]
        );
    }

    private function getErrorType(Throwable $exception): string
    {
        return match (true) {
            $exception instanceof PDOException => self::DB_ERROR_TYPE,
            default => self::DB_GENERAL_TYPE,
        };
    }
}
