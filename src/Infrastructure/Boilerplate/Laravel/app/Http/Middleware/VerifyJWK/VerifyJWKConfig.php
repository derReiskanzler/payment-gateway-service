<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class VerifyJWKConfig
{
    /**
     * @param string[]             $audiences
     * @param string[]             $issuers
     * @param string[]             $algorithms
     * @param array<string, mixed> $additionalClaims
     */
    public function __construct(
        private string $jwkPath,
        private array $audiences,
        private array $issuers,
        private array $algorithms,
        private array $additionalClaims,
    ) {
    }

    public function getJwkPath(): string
    {
        return $this->jwkPath;
    }

    /**
     * @return string[]
     */
    public function getAudiences(): array
    {
        return $this->audiences;
    }

    /**
     * @return string[]
     */
    public function getIssuers(): array
    {
        return $this->issuers;
    }

    /**
     * @return string[]
     */
    public function getAlgorithms(): array
    {
        return $this->algorithms;
    }

    /**
     * @return string[]
     */
    public function getAdditionalClaims(): array
    {
        return $this->additionalClaims;
    }

    /**
     * @return array{jwkPath: string, audiences: array<string>, issuers: array<string>, algorithms: array<string>, additionalClaims: array<string, mixed>}
     */
    public function toArray(): array
    {
        return [
            'jwkPath' => $this->jwkPath,
            'audiences' => $this->audiences,
            'issuers' => $this->issuers,
            'algorithms' => $this->algorithms,
            'additionalClaims' => $this->additionalClaims,
        ];
    }
}
