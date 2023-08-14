<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\ExternalApi;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\WebhookSecret;

final class StripeWebhookConfig
{
    public const WEBHOOK_SECRET = 'webhook_secret';

    /**
     * @param string[] $configData
     */
    public static function fromArray(array $configData): self
    {
        return new self(
            WebhookSecret::fromString($configData[self::WEBHOOK_SECRET]),
        );
    }

    private function __construct(
        private WebhookSecret $webhookSecret,
    ) {
    }

    public function webhookSecret(): WebhookSecret
    {
        return $this->webhookSecret;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            self::WEBHOOK_SECRET => $this->webhookSecret->toString(),
        ];
    }
}
