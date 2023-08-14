<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Api\Deprecation;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\ResponseDeprecation;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ResponseDeprecationTest extends TestCase
{
    public function testDeprecateFacade(): void
    {
        $deprecationFacade = new ResponseDeprecation();

        $mockedRequest = $this->mockRequest();
        $mockedResponse = $this->mockResponse();
        $mockedConfiguration = $this->mockConfiguration();

        $mockedProjectionLogger = Mockery::mock(LoggerInterface::class)
            ->makePartial()
            ->expects('warning')
            ->andReturnNull()
            ->getMock();
        /* should be moved to integration */
        app()->bind(LoggerInterface::class, static fn () => $mockedProjectionLogger);

        $response = $deprecationFacade->deprecate($mockedRequest, $mockedResponse, $mockedConfiguration);

        $mockedProjectionLogger->mockery_verify();
        static::assertArrayHasKey('deprecation', $response->headers->all());
        static::assertArrayHasKey('link', $response->headers->all());
        static::assertArrayHasKey('sunset', $response->headers->all());
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

    /**
     * @return MockInterface&DeprecationConfigurationInterface
     */
    private function mockConfiguration(): DeprecationConfigurationInterface
    {
        $mockedConfiguration = Mockery::mock(DeprecationConfigurationInterface::class)->makePartial();
        $mockedConfiguration->allows('getDeprecation')->andReturns('Sun, 19 Apr 2020 10:00:00 GMT');
        $mockedConfiguration->allows('getLink')->andReturns('http://www.google.com');
        $mockedConfiguration->allows('getSunset')->andReturns('Mon, 20 May 2020 10:00:00 GMT')->getMock();

        return $mockedConfiguration;
    }

    /**
     * @return MockInterface&Response
     */
    private function mockResponse(): Response
    {
        $mockedResponse = Mockery::mock(Response::class)->makePartial();
        $mockedResponse->headers = new ResponseHeaderBag([]);

        return $mockedResponse;
    }
}
