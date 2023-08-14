<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util;

use Exception;
use Illuminate\Log\LogManager;

/**
 * Class TraceIdProvider.
 *
 * Trace Id is taken from request header if exists. Else generated and stored for request lifetime.
 * Header format is compatible with Zipkin.
 *
 * @see https://zipkin.io/
 */
class TraceIdProvider implements TraceIdProviderInterface
{
    public const HEADER_NAME = 'x-b3-traceid';
    private const ALLOWED_TRACE_ID_CHARS = '0123456789abcdefghijklmnopqrstuvwxyz';
    private const TRACE_ID_LENGTH = 32;

    private ?string $traceId;

    public function getTraceId(): string
    {
        return $this->traceId ?? $this->getTraceIdFromHeaderIfExists() ?? $this->generateAndSaveNewTraceId();
    }

    private function getTraceIdFromHeaderIfExists(): ?string
    {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, \CASE_LOWER);

        if (isset($headers[self::HEADER_NAME])) {
            $this->traceId = $headers[self::HEADER_NAME];
            $this->checkTraceIdFormat(); // log warning but don't fail (non-critical flow)

            return $headers[self::HEADER_NAME];
        }

        return null;
    }

    /**
     * @throws Exception if an appropriate source of randomness cannot be found for random_int()
     */
    private function generateAndSaveNewTraceId(): string
    {
        $traceId = '';

        for ($i = 0; $i < self::TRACE_ID_LENGTH; ++$i) {
            $traceId .= self::ALLOWED_TRACE_ID_CHARS[random_int(0, \strlen(self::ALLOWED_TRACE_ID_CHARS) - 1)];
        }

        $this->traceId = $traceId;

        return $this->traceId;
    }

    private function checkTraceIdFormat(): void
    {
        if (null !== $this->traceId && preg_match('/[^a-z0-9]/', $this->traceId)) {
            $this->logInvalidTraceIdFormat();
        }

        if (null !== $this->traceId && self::TRACE_ID_LENGTH !== \strlen($this->traceId)) {
            $this->logInvalidTraceIdFormat();
        }
    }

    private function logInvalidTraceIdFormat(): void
    {
        /** @var LogManager $logger */
        $logger = app()->get('log'); // due to issue, described in TraceIdLoggingProcessor, logger redeemed "magically"
        $logger->warning('Invalid trace id format');
    }
}
