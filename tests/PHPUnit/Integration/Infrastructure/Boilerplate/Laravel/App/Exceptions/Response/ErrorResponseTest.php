<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Laravel\App\Exceptions\Response;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response\ErrorResponse;
use Tests\TestCase;

class ErrorResponseTest extends TestCase
{
    public function testAssertCodeErrorResponse(): void
    {
        $errorResponse = new ErrorResponse('Unauthorized', 401);

        $response = $errorResponse->getResponse();

        static::assertSame(401, $response->getStatusCode());
    }

    public function testAssertMessageErrorResponse(): void
    {
        $errorResponse = new ErrorResponse('Unauthorized', 401);

        $response = $errorResponse->getResponse();

        static::assertSame('Unauthorized', json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR)['message']);
    }

    public function testAssertCompleteContentErrorResponse(): void
    {
        $errorResponse = new ErrorResponse('Unauthorized', 401);

        $response = $errorResponse->getResponse();

        static::assertSame('{"code":401,"message":"Unauthorized"}', $response->getContent());
    }
}
