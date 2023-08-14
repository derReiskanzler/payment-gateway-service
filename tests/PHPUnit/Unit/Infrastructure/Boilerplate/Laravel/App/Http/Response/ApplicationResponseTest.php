<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Laravel\App\Http\Response;

use Allmyhomes\DDDAbstractions\Application\AbstractTransformer;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response\ApplicationResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ApplicationResponseTest extends TestCase
{
    private ApplicationResponse $applicationResponse;
    /**
     * @var array<string, int|string>
     */
    private array $data;
    private AbstractTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new class() extends AbstractTransformer {
            /**
             * @return array<string, string>
             */
            public function transform(): array
            {
                return [];
            }
        };
        $this->applicationResponse = new ApplicationResponse();

        $now = date('Y-m-d H:i:s');
        $this->data = [
            'name' => 'Agent One',
            'email' => 'agent1@allmyhomes.com',
            'role' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $this->applicationResponse = new ApplicationResponse();
    }

    public function testItem(): void
    {
        $response = $this->applicationResponse->item($this->data, $this->transformer);
        $content = (array) json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR);
        static::assertSame('application/json', $response->headers->get('Content-Type'));
        static::assertSame(Response::HTTP_OK, $response->getStatusCode());
        static::assertArrayHasKey('data', $content);
        static::assertArrayNotHasKey('meta', $content);
    }

    public function testCreated(): void
    {
        $response = $this->applicationResponse->created();
        static::assertSame('application/json', $response->headers->get('Content-Type'));
        static::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testNoContent(): void
    {
        $response = $this->applicationResponse->noContent();
        static::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        static::assertSame('application/json', $response->headers->get('Content-Type'));
    }
}
