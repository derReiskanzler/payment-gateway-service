<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Logging\Processors;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProviderInterface;
use Illuminate\Log\Logger as IlluminateLogger;
use Monolog\Logger as MonologLogger;

/**
 * Class TraceIdLoggingProcessor.
 *
 * I am not proud of what I have done here.
 * Laravel as a bad junky friend is pulling the whole coding paradigm into the abyss of ignorance.
 *
 * This processor is registered as 'tap' configuration in src/Infrastructure/Boilerplate/Laravel/config/logging.php because there seem to be
 * no proper way of adding a custom field into log.
 *
 * @see https://github.com/laravel/ideas/issues/1796
 *
 * It also cannot be done through event listening because inside of the listener there is no log message context.
 */
class TraceIdLoggingProcessor
{
    private const LOG_FIELD = 'x-b3-traceid';

    private TraceIdProviderInterface $traceIdProvider;

    public function __construct(TraceIdProviderInterface $traceIdProvider)
    {
        $this->traceIdProvider = $traceIdProvider;
    }

    public function __invoke(IlluminateLogger $logger): void
    {
        if (!$logger->getLogger() instanceof MonologLogger) {
            return;
        }

        foreach ($logger->getLogger()->getHandlers() as $handler) {
            $handler->pushProcessor(function ($record) {
                $record['extra'][self::LOG_FIELD] = $this->traceIdProvider->getTraceId();

                return $record;
            });
        }
    }
}
