<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Listeners;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProvider;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProviderInterface;
use Exception;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Psr\Log\LoggerInterface;

class TraceIdResponseEnricher
{
    public function __construct(
        private TraceIdProviderInterface $traceIdProvider,
        private LoggerInterface $logger
    ) {
    }

    public function handle(RequestHandled $event): void
    {
        try {
            $response = $event->response;
            $response->headers->add([TraceIdProvider::HEADER_NAME => $this->traceIdProvider->getTraceId()]);
        } catch (Exception $exception) {
            $this->logger->alert('can not add traceId to Response', [
                'traceid_error_msg' => $exception->getMessage(),
            ]);
        }
    }
}
