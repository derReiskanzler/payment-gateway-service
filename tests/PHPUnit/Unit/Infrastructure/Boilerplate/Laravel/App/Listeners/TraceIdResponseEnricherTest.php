<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Laravel\App\Listeners;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProviderInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Listeners\TraceIdResponseEnricher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Response;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TraceIdResponseEnricherTest extends TestCase
{
    public function testResponseEnricher(): void
    {
        /** @var LoggerInterface&MockInterface $mockedLogger */
        $mockedLogger = Mockery::mock(LoggerInterface::class)
            ->makePartial()
            ->allows('alert')
            ->andReturnNull()
            ->getMock();

        /** @var TraceIdProviderInterface&MockInterface $traceIdProvider */
        $traceIdProvider = Mockery::mock(TraceIdProviderInterface::class)
            ->makePartial()
            ->allows('getTraceId')
            ->andReturns('m6v8t4fmhy3y1lzf3awfi6qgu28zu7v3')
            ->getMock();

        /** @var MockInterface&Response $response */
        $response = Mockery::mock(Response::class)->makePartial();
        $response->headers = new ResponseHeaderBag([]);
        $requestHandled = Mockery::mock(RequestHandled::class, [null, $response]);
        $traceIdResponseEnricher = new TraceIdResponseEnricher($traceIdProvider, $mockedLogger);

        $traceIdResponseEnricher->handle($requestHandled);

        static::assertSame('m6v8t4fmhy3y1lzf3awfi6qgu28zu7v3', $response->headers->get('x-b3-traceid'));
    }

    public function testIfResponseIsNull(): void
    {
        /** @var LoggerInterface&MockInterface $mockedLogger */
        $mockedLogger = Mockery::mock(LoggerInterface::class)
            ->makePartial()
            ->expects('alert')
            ->andReturnNull()
            ->getMock();

        /** @var TraceIdProviderInterface&MockInterface $traceIdProvider */
        $traceIdProvider = Mockery::mock(TraceIdProviderInterface::class)
            ->makePartial()
            ->allows('getTraceId')
            ->andReturns('m6v8t4fmhy3y1lzf3awfi6qgu28zu7v3')
            ->getMock();

        /** @var RequestHandled&MockInterface $requestHandled */
        $requestHandled = Mockery::mock(RequestHandled::class, [null, null]);
        $traceIdResponseEnricher = new TraceIdResponseEnricher($traceIdProvider, $mockedLogger);

        $traceIdResponseEnricher->handle($requestHandled);

        $mockedLogger->mockery_verify();
        static::assertSame(1, $mockedLogger->mockery_getExpectationCount());
    }
}
