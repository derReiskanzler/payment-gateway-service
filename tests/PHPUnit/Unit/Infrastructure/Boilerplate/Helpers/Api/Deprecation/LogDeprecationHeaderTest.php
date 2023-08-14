<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Api\Deprecation;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\LogDeprecationHeader;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\ResponseDeprecationInterface;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class LogDeprecationHeaderTest extends TestCase
{
    public function testClassInitialization(): void
    {
        $mockedWrapped = Mockery::mock(ResponseDeprecationInterface::class);
        $mockedRequest = Mockery::mock(Request::class);
        $mockedLogger = Mockery::mock(LoggerInterface::class);
        $logDeprecationHeader = new LogDeprecationHeader($mockedWrapped, $mockedRequest, $mockedLogger);

        static::assertInstanceOf(ResponseDeprecationInterface::class, $logDeprecationHeader);
    }

    public function testLogDeprecationHeader(): void
    {
        $mockedRequest = $this->mockRequest();
        /** @var Response&MockInterface $mockedResponse */
        $mockedResponse = Mockery::mock(Response::class);

        /** @var ResponseDeprecationInterface&MockInterface $mockedWrapped */
        $mockedWrapped = Mockery::mock(ResponseDeprecationInterface::class)
            ->makePartial()
            ->allows('deprecate')
            ->andReturns($mockedResponse)
            ->getMock();

        /** @var LoggerInterface&MockInterface $mockedLogger */
        $mockedLogger = Mockery::mock(LoggerInterface::class)
            ->makePartial()
            ->expects('warning')
            ->getMock();

        $logDeprecationHeader = new LogDeprecationHeader($mockedWrapped, $mockedRequest, $mockedLogger);

        $logDeprecationHeader->deprecate($mockedResponse);

        $mockedLogger->mockery_verify();

        $this->addToAssertionCount(1);
    }

    /**
     * @return MockInterface&Request
     */
    private function mockRequest(): Request
    {
        $mockedRequest = Mockery::mock(Request::class)->makePartial();
        $mockedRequest->allows('get')->andReturns(['aud' => 15]);
        $mockedRequest->allows('getMethod')->andReturns('POST');
        $mockedRequest->allows('getPathInfo')->andReturns('/v1/users');
        $mockedRequest->allows('getUri')->andReturns('http://api.dev.local/v1/users');
        $mockedRequest->allows('fullUrl')->andReturns('http://api.dev.local/v1/users');
        $mockedRequest->allows('getUserInfo')->andReturns(null)->getMock();

        return $mockedRequest;
    }
}
