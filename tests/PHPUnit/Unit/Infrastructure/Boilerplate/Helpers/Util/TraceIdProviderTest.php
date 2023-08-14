<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Util;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProvider;
use PHPUnit\Framework\TestCase;

class TraceIdProviderTest extends TestCase
{
    public function testGetTraceIdFromHeader(): void
    {
        $_SERVER['HTTP_x-b3-traceid'] = 'm6v8t4fmhy3y1lzf3awfi6qgu28zu7v2';
        $traceIdProdiver = new TraceIdProvider();

        $traceId = $traceIdProdiver->getTraceId();

        static::assertSame('m6v8t4fmhy3y1lzf3awfi6qgu28zu7v2', $traceId);
    }

    public function testGenerateNewTraceId(): void
    {
        $traceIdProdiver = new TraceIdProvider();

        $traceId = $traceIdProdiver->getTraceId();

        static::assertSame(32, \strlen($traceId));
    }
}
