<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Laravel\App\Http\Middleware;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\DeprecationResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Tests\TestCase;

class DeprecationResponseTest extends TestCase
{
    public function testDeprecationMiddlewareAddsHeaders(): void
    {
        $deprecateResponseMiddleware = new DeprecationResponse();

        $request = $this->mockRequest();
        $response = $this->mockResponse();

        $deprecateResponseMiddleware->handle($request, static function () use (&$response) {
            return $response;
        });

        static::assertArrayHasKey('deprecation', $response->headers->all());
        static::assertArrayHasKey('link', $response->headers->all());
        static::assertArrayHasKey('sunset', $response->headers->all());
    }

    /**
     * @return MockInterface&Request
     */
    private function mockRequest(): Request
    {
        $route = Mockery::mock(Route::class)
            ->allows('getAction')
            ->withAnyArgs()
            ->andReturns([
                'deprecation' => true,
                'link' => 'http://www.google.com',
                'sunset' => '2020-05-25 23:59:59',
            ])
            ->getMock();

        $mockedRequest = Mockery::mock(Request::class)->makePartial();
        $mockedRequest->allows('route')->withNoArgs()->andReturns($route);
        $mockedRequest->allows('get')->andReturns(['aud' => 15]);
        $mockedRequest->allows('getMethod')->andReturns('POST');
        $mockedRequest->allows('getPathInfo')->andReturns('/v1/users');
        $mockedRequest->allows('getUri')->andReturns('http://api.dev.local/v1/users');
        $mockedRequest->allows('fullUrl')->andReturns('http://api.dev.local/v1/users');
        $mockedRequest->allows('getUserInfo')->andReturns(null)->getMock();

        return $mockedRequest;
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
