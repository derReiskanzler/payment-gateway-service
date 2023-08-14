<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Api\Infrastructure\Boilerplate\Laravel\App\Http\Controllers;

use Tests\TestCase;

class HealthzControllerApiTest extends TestCase
{
    protected string $endpoint = '/healthz';

    public function testGetHealthz(): void
    {
        $response = $this->get($this->endpoint);
        $response->assertStatus(200);
    }
}
