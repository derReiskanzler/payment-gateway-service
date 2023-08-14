<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Util\ExternalApi;

use Allmyhomes\Infrastructure\Util\ExternalApi\StripeServiceConfig;
use Generator;
use PHPUnit\Framework\TestCase;

final class StripeServiceConfigTest extends TestCase
{
    /**
     * @param string[] $config
     * @dataProvider provideStripeApiClientConfigData
     */
    public function testFromArray(array $config): void
    {
        $stripeApiClientConfig = StripeServiceConfig::fromArray($config);

        self::assertInstanceOf(
            StripeServiceConfig::class,
            $stripeApiClientConfig,
            'stripe api client config does not match expected class: StripeServiceConfig.'
        );
    }

    /**
     * @param string[] $config
     * @dataProvider provideStripeApiClientConfigData
     */
    public function testApiKey(array $config): void
    {
        $stripeApiClientConfig = StripeServiceConfig::fromArray($config);

        self::assertEquals(
            $config[StripeServiceConfig::API_KEY],
            $stripeApiClientConfig->apiKey(),
            'stripe api client config api key does not match expected string.'
        );
    }

    /**
     * @param string[] $config
     * @dataProvider provideStripeApiClientConfigData
     */
    public function testMode(array $config): void
    {
        $stripeApiClientConfig = StripeServiceConfig::fromArray($config);

        self::assertEquals(
            $config[StripeServiceConfig::MODE],
            $stripeApiClientConfig->mode(),
            'stripe api client config api key does not match expected string.'
        );
    }

    /**
     * @param string[] $config
     * @dataProvider provideStripeApiClientConfigData
     */
    public function testSuccessUrl(array $config): void
    {
        $stripeApiClientConfig = StripeServiceConfig::fromArray($config);

        self::assertEquals(
            $config[StripeServiceConfig::SUCCESS_URL],
            $stripeApiClientConfig->successUrl(),
            'stripe api client config api key does not match expected string.'
        );
    }

    /**
     * @param string[] $config
     * @dataProvider provideStripeApiClientConfigData
     */
    public function testCancelUrl(array $config): void
    {
        $stripeApiClientConfig = StripeServiceConfig::fromArray($config);

        self::assertEquals(
            $config[StripeServiceConfig::CANCEL_URL],
            $stripeApiClientConfig->cancelUrl(),
            'stripe api client config api key does not match expected string.'
        );
    }

    /**
     * @param string[] $config
     * @dataProvider provideStripeApiClientConfigData
     */
    public function testToArray(array $config): void
    {
        $stripeApiClientConfig = StripeServiceConfig::fromArray($config);

        self::assertEquals(
            $config,
            $stripeApiClientConfig->toArray(),
            'stripe api client config to array does not match expected array.'
        );
    }

    public function provideStripeApiClientConfigData(): Generator
    {
        yield 'StripeServiceConfig data' => [
            'StripeServiceConfig array data' => [
                StripeServiceConfig::API_KEY => 'sk_test_51L153aLHVthCw2smA7c2NqAfnvnpOzV03wSynsJXtDp97wodZqpkyDL2AOuW6ZeZLVVivEkUv0Oodbq4cSvMzl7400k13X30Ep',
                StripeServiceConfig::MODE => 'payment',
                StripeServiceConfig::SUCCESS_URL => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
                StripeServiceConfig::CANCEL_URL => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
            ],
        ];
    }
}
