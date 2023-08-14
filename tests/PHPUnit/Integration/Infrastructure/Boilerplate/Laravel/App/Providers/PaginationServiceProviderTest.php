<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Laravel\App\Providers;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response\ApplicationResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\doubles\ResponseTransformerDouble;
use Tests\TestCase;

class PaginationServiceProviderTest extends TestCase
{
    public function testPaginatorQuery(): void
    {
        $request = new Request([
            'name' => 'boilerplate',
            'user_name' => 'test',
        ]);

        app()->bind('request', static fn () => $request);

        $data = app()->makeWith(LengthAwarePaginator::class, [
            'items' => [
                'test_1',
                'test_2',
                'test_2',
            ],
            'total' => 3,
            'perPage' => 1,
            'currentPage' => 2,
        ]);
        $applicationResponse = new ApplicationResponse();
        $response = $applicationResponse->paginator($data, new ResponseTransformerDouble());

        $responseLinks = (json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR))['meta']['pagination']['links'];
        static::assertSame('/?name=boilerplate&user_name=test&page=1', $responseLinks['previous']);
        static::assertSame('/?name=boilerplate&user_name=test&page=3', $responseLinks['next']);
    }

    public function testPaginatorAndQueryPassedWithAttributes(): void
    {
        $request = new Request([
            'name' => 'boilerplate',
            'user_name' => 'test',
        ]);

        app()->bind('request', static fn () => $request);

        $data = app()->makeWith(LengthAwarePaginator::class, [
            'items' => [
                'test_1',
                'test_2',
                'test_2',
            ],
            'total' => 3,
            'perPage' => 1,
            'currentPage' => 2,
            'options' => [
                'query' => [
                    'user_name' => 'test',
                    'role' => 4,
                ],
            ],
        ]);
        $applicationResponse = new ApplicationResponse();
        $response = $applicationResponse->paginator($data, new ResponseTransformerDouble());

        $responseLinks = (json_decode($response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR))['meta']['pagination']['links'];
        static::assertSame('/?user_name=test&role=4&name=boilerplate&page=1', $responseLinks['previous']);
        static::assertSame('/?user_name=test&role=4&name=boilerplate&page=3', $responseLinks['next']);
    }
}
