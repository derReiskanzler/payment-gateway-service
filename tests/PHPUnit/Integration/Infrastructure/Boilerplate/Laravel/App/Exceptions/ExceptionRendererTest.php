<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Laravel\App\Exceptions;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\ExceptionRenderer;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response\ApplicationResponse;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Traits\ResponseFormatTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

class ExceptionRendererTest extends TestCase
{
    /**
     * @var Request&MockObject
     */
    private mixed $request;

    /**
     * @var ResponseFactory&MockObject
     */
    private mixed $mockedFactory;

    /**
     * @var Response&MockObject
     */
    private mixed $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(Request::class);
        $this->response = $this->createMock(Response::class);
        $this->mockedFactory = $this->createMock(ResponseFactory::class);
        $this->app->bind(ResponseFactory::class, fn () => $this->mockedFactory);
    }

    public function testGetResponse(): void
    {
        $controller = new class() {
            use ResponseFormatTrait;
        }; // phpcs:ignore
        static::assertInstanceOf(ApplicationResponse::class, $controller->response);
    }

    /**
     * @param array<string> $expectedValue
     *
     * @dataProvider exceptionDataProvider
     */
    public function testValidHttpResponse(Exception $exception, array $expectedValue): void
    {
        $exceptionRenderer = new ExceptionRenderer();

        $this->mockedFactory
            ->expects(static::once())
            ->method('make')
            ->with(
                [
                    'code' => $expectedValue['code'],
                    'message' => $expectedValue['message'],
                ],
            )
            ->willReturn($this->response);

        $exceptionRenderer->render($this->request, $exception);
    }

    /**
     * @return array<array<Exception|array<string, string|int>>>
     */
    public function exceptionDataProvider(): array
    {
        return [
            [new Exception('Error Message = 0', 0), ['code' => 500, 'message' => 'Error Message = 0']],
            [new Exception('Error Message = 600', 600), ['code' => 500, 'message' => 'Error Message = 600']],
            [new Exception('Error Message = 99', 600), ['code' => 500, 'message' => 'Error Message = 99']],
            [new Exception('Bad Request', 400), ['code' => 400, 'message' => 'Bad Request']],
            [new Exception('Bad Request with Invalid Code', 444), ['code' => 500, 'message' => 'Bad Request with Invalid Code']],
            [new Exception('Internal Server Error', 510), ['code' => 510, 'message' => 'Internal Server Error']],
            [new Exception('', 510), ['code' => 510, 'message' => 'Internal Server Error']],
        ];
    }
}
