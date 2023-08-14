<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Api\Infrastructure\Boilerplate\Laravel\App\Http\Controllers;

use Tests\TestCase;

class CatchRouteNotAvailableApiTest extends TestCase
{
    protected string $endpoint = '/healthz';

    public function testDoNotCatchAvailableRoute(): void
    {
        $response = $this->get($this->endpoint);
        $response->assertStatus(200);
    }

    public function testCatchNotAvailableRoute(): void
    {
        $response = $this->get('/invalid-endpoint');
        $response->assertStatus(406);
    }
}
