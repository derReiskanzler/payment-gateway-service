<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\ConfigFactories;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\VerifyJWKConfig;
use function config;

class VerifyJWKConfigFactory extends AbstractVerifyJWKConfigFactory
{
    public function createConfigForTokenProvider(string $tokenProvider): VerifyJWKConfig
    {
        $config = config('verify-jwk.'.$tokenProvider);

        return $this->configFromArray($config);
    }
}
