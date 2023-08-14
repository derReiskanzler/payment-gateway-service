<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Util\ExternalApi;

use Allmyhomes\Infrastructure\Util\ExternalApi\StripeSignatureCheckService;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeWebhookConfig;
use Generator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

final class StripeSignatureCheckServiceTest extends TestCase
{
    private StripeWebhookConfig $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = StripeWebhookConfig::fromArray([
            StripeWebhookConfig::WEBHOOK_SECRET => 'whsec_XL6bT8tMjsKF8lEIERE4ylMEVvfDsUxW',
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @dataProvider provideStripeServiceData
     */
    public function testHasValidSignature(string $webhookJsonContent, string $webhookSignature): void
    {
        $webhook = Mockery::mock('alias:'.Webhook::class);
        $webhook
            ->shouldReceive('constructEvent')
            ->once()
            ->andReturnSelf();

        $service = new StripeSignatureCheckService($this->config);

        $result = $service->hasValidSignature(
            $webhookJsonContent,
            $webhookSignature,
        );

        self::assertInstanceOf(
            Webhook::class,
            $webhook,
            'created stripe webhook does not match expected class: Webhook.'
        );
        self::assertEquals(
            true,
            $result,
            'validation of webhook does not match expected bool.'
        );
    }

    /**
     * @dataProvider provideStripeServiceData
     */
    public function testHasInvalidSignature(string $webhookJsonContent, string $webhookSignature): void
    {
        $webhook = Mockery::mock('alias:'.Webhook::class);
        $webhook
            ->shouldReceive('constructEvent')
            ->once()
            ->andThrow(SignatureVerificationException::class);

        $service = new StripeSignatureCheckService($this->config);

        $result = $service->hasValidSignature(
            $webhookJsonContent,
            $webhookSignature,
        );

        self::assertIsString(
            $result,
            'validation of webhook is not a string.'
        );
    }

    public function provideStripeServiceData(): Generator
    {
        yield 'StripeSignatureCheckService data' => [
            'webhook json content' => 'json content',
            'webhook signature' => 't=1656348754,v1=9df19b5581e3c1789648ec38b2e726bcc5160871b8a6722b14af8b001ee1e87a,v0=8e074bccc0127714634d89fc6bca606055b3d6b1e069f00f961d75ea7516f729',
        ];
    }
}
