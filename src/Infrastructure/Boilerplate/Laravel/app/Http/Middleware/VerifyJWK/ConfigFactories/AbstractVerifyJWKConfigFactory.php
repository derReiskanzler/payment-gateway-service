<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\ConfigFactories;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\VerifyJWKConfig;

abstract class AbstractVerifyJWKConfigFactory
{
    abstract public function createConfigForTokenProvider(string $tokenProvider): VerifyJWKConfig;

    /**
     * @param array{jwkPath: string|null, audiences: array<string>|null, issuers: array<string>|null, algorithms: array<string>|null, additionalClaims: array<string, mixed>|null} $config
     */
    protected function configFromArray(array $config): VerifyJWKConfig
    {
        return new VerifyJWKConfig(
            $config['jwkPath'] ?? throw $this->missingConfigException($config, 'jwkPath'),
            $config['audiences'] ?? throw $this->missingConfigException($config, 'audiences'),
            $config['issuers'] ?? throw $this->missingConfigException($config, 'issuers'),
            $config['algorithms'] ?? throw $this->missingConfigException($config, 'algorithms'),
            $config['additionalClaims'] ?? [],
        );
    }

    /**
     * @param array{jwkPath: string|null, audiences: array<string>|null, issuers: array<string>|null, algorithms: array<string>|null, additionalClaims: array<string, mixed>|null} $config
     */
    private function missingConfigException(array $config, string $key): \InvalidArgumentException
    {
        /* @noinspection JsonEncodingApiUsageInspection */
        return new \InvalidArgumentException('The config '.json_encode($config, \JSON_PARTIAL_OUTPUT_ON_ERROR).' does not contain the required key '.$key);
    }
}
