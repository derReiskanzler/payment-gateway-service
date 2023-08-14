<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Laravel\App\Http\Traits;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response\ApplicationResponse;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Traits\ResponseFormatTrait;
use PHPUnit\Framework\TestCase;

class ResponseFormatTest extends TestCase
{
    public function testGetResponse(): void
    {
        $controller = new class() {
            use ResponseFormatTrait;
        }; // phpcs:ignore
        static::assertInstanceOf(ApplicationResponse::class, $controller->response);
    }
}
