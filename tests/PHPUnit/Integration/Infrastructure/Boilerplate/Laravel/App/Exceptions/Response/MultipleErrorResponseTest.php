<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Laravel\App\Exceptions\Response;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response\MultipleErrorResponse;
use Tests\TestCase;

class MultipleErrorResponseTest extends TestCase
{
    public function testAssertCodeSingleError(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is required']]);

        $response = $errorResponse->getResponse();

        static::assertSame(400, $response->getStatusCode());
    }

    public function testAssertMessageSingleError(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is required']]);

        $response = $errorResponse->getResponse();

        static::assertSame('Single Error', json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR)['message']);
    }

    public function testAssertErrorSingleError(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is required']]);

        $response = $errorResponse->getResponse();

        static::assertSame([['field' => 'id', 'message' => 'id is required']], json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR)['errors']);
    }

    public function testAssertCompleteContentSingleError(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is required']]);

        $response = $errorResponse->getResponse();

        static::assertSame('{"code":400,"message":"Single Error","errors":[{"field":"id","message":"id is required"}]}', $response->getContent());
    }

    public function testAssertCodeMultipleErrors(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short', 'id must be uuid']]);

        $response = $errorResponse->getResponse();

        static::assertSame(400, $response->getStatusCode());
    }

    public function testAssertMessageMultipleErrors(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short', 'id must be uuid']]);

        $response = $errorResponse->getResponse();

        static::assertSame('Single Error', json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR)['message']);
    }

    public function testAssertErrorMultipleErrors(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short', 'id must be uuid']]);

        $response = $errorResponse->getResponse();

        static::assertSame(
            [['field' => 'id', 'message' => 'id is short'], ['field' => 'id', 'message' => 'id must be uuid']],
            json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR)['errors']
        );
    }

    public function testAssertCompleteContentMultipleErrors(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short', 'id must be uuid']]);

        $response = $errorResponse->getResponse();

        static::assertSame(
            '{"code":400,"message":"Single Error","errors":[{"field":"id","message":"id is short"},{"field":"id","message":"id must be uuid"}]}',
            $response->getContent()
        );
    }

    public function testAssertCodeMultipleErrorsDifferentField(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short'], 'message' => ['message min 6 chars']]);

        $response = $errorResponse->getResponse();

        static::assertSame(400, $response->getStatusCode());
    }

    public function testAssertMessageMultipleErrorsDifferentField(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short'], 'message' => ['message min 6 chars']]);

        $response = $errorResponse->getResponse();

        static::assertSame('Single Error', json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR)['message']);
    }

    public function testAssertErrorMultipleErrorsDifferentField(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short'], 'message' => ['message min 6 chars']]);

        $response = $errorResponse->getResponse();

        static::assertSame(
            [['field' => 'id', 'message' => 'id is short'], ['field' => 'message', 'message' => 'message min 6 chars']],
            json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR)['errors']
        );
    }

    public function testAssertCompleteContentMultipleErrorsDifferentField(): void
    {
        $errorResponse = new MultipleErrorResponse('Single Error', 400, ['id' => ['id is short'], 'message' => ['message min 6 chars']]);

        $response = $errorResponse->getResponse();

        static::assertSame(
            '{"code":400,"message":"Single Error","errors":[{"field":"id","message":"id is short"},{"field":"message","message":"message min 6 chars"}]}',
            $response->getContent()
        );
    }
}
