<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\ConfigFactories;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\VerifyJWKConfig;

class VerifyJWKConfigArrayFactory extends AbstractVerifyJWKConfigFactory
{
    /**
     * @var array<string, array{jwkPath: string|null, audiences: array<string>|null, issuers: array<string>|null, algorithms: array<string>|null, additionalClaims: array<string, mixed>|null}>
     */
    private array $configs = [];

    /**
     * @param array{jwkPath: string|null, audiences: array<string>|null, issuers: array<string>|null, algorithms: array<string>|null, additionalClaims: array<string, mixed>|null} $config
     */
    public function addConfig(string $tokenProvider, array $config): void
    {
        $this->configs[$tokenProvider] = $config;
    }

    public function createConfigForTokenProvider(string $tokenProvider): VerifyJWKConfig
    {
        return $this->configFromArray($this->configs[$tokenProvider]);
    }
}
