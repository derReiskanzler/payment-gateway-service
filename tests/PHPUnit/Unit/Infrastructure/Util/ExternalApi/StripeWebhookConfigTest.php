<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Util\ExternalApi;

use Allmyhomes\Infrastructure\Util\ExternalApi\StripeWebhookConfig;
use Generator;
use PHPUnit\Framework\TestCase;

final class StripeWebhookConfigTest extends TestCase
{
    /**
     * @param string[] $config
     * @dataProvider provideStripeWebhookConfigData
     */
    public function testFromArray(array $config): void
    {
        $stripeWebhookConfig = StripeWebhookConfig::fromArray($config);

        self::assertInstanceOf(
            StripeWebhookConfig::class,
            $stripeWebhookConfig,
            'stripe webhook config does not match expected class: StripeWebhookConfig.'
        );
    }

    /**
     * @param string[] $config
     * @dataProvider provideStripeWebhookConfigData
     */
    public function testWebhookSecret(array $config): void
    {
        $stripeWebhookConfig = StripeWebhookConfig::fromArray($config);

        self::assertEquals(
            $config[StripeWebhookConfig::WEBHOOK_SECRET],
            $stripeWebhookConfig->webhookSecret()->toString(),
            'stripe webhook config webhook secret does not match expected string.'
        );
    }

    /**
     * @param string[] $config
     * @dataProvider provideStripeWebhookConfigData
     */
    public function testToArray(array $config): void
    {
        $stripeWebhookConfig = StripeWebhookConfig::fromArray($config);

        self::assertEquals(
            $config,
            $stripeWebhookConfig->toArray(),
            'stripe webhook config to array does not match expected array.'
        );
    }

    public function provideStripeWebhookConfigData(): Generator
    {
        yield 'StripeWebhookConfig data' => [
            'StripeWebhookConfig array data' => [
                StripeWebhookConfig::WEBHOOK_SECRET => 'whsec_XL6bT8tMjsKF8lEIERE4ylMEVvfDsUxW',
            ],
        ];
    }
}
