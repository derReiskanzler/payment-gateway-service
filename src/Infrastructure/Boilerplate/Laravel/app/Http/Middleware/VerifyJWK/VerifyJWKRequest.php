<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class VerifyJWKRequest
{
    private string $token;
    private string $uri;
    private string $tokenProvider;

    public function __construct(
        Request $request
    ) {
        $route = $request->route();
        if (!$route instanceof Route) {
            throw new \RuntimeException('Route could not be extracted from the request.');
        }

        $this->token = $request->bearerToken() ?? throw new \InvalidArgumentException('No Bearer-Token provided!');
        $this->uri = $route->uri();
        $this->tokenProvider = $route->getAction('tokenProvider') ?? throw new \InvalidArgumentException('Missing tokenProvider-Config for this route!');
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getTokenProvider(): string
    {
        return $this->tokenProvider;
    }

    /**
     * @return array{token: string, uri: string, tokenProvider: string}
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'uri' => $this->uri,
            'tokenProvider' => $this->tokenProvider,
        ];
    }
}
