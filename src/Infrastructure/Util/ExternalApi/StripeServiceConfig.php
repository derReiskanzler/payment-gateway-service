<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\ExternalApi;

final class StripeServiceConfig
{
    public const API_KEY = 'api_key';
    public const MODE = 'mode';
    public const SUCCESS_URL = 'success_url';
    public const CANCEL_URL = 'cancel_url';

    /**
     * @param string[] $configData
     */
    public static function fromArray(array $configData): self
    {
        return new self(
            $configData[self::API_KEY],
            $configData[self::MODE],
            $configData[self::SUCCESS_URL],
            $configData[self::CANCEL_URL],
        );
    }

    private function __construct(
        private string $apiKey,
        private string $mode,
        private string $successUrl,
        private string $cancelUrl,
    ) {
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function successUrl(): string
    {
        return $this->successUrl;
    }

    public function cancelUrl(): string
    {
        return $this->cancelUrl;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            self::API_KEY => $this->apiKey,
            self::MODE => $this->mode,
            self::SUCCESS_URL => $this->successUrl,
            self::CANCEL_URL => $this->cancelUrl,
        ];
    }
}
