<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\WebhookSecret;
use PHPUnit\Framework\TestCase;

final class WebhookSecretTest extends TestCase
{
    public function testFromString(): void
    {
        $webhookSecret = WebhookSecret::fromString('whsec_XL6bT8tMjsKF8lEIERE4ylMEVvfDsUxW');

        $this->assertInstanceOf(
            WebhookSecret::class,
            $webhookSecret,
            'created webhook secret from string does not match expected class: WebhookSecret.'
        );
    }

    public function testToString(): void
    {
        $string = 'whsec_XL6bT8tMjsKF8lEIERE4ylMEVvfDsUxW';
        $webhookSecret = WebhookSecret::fromString($string);

        $this->assertEquals(
            $string,
            $webhookSecret->toString(),
            'webhook secret to string does not match expected string.',
        );
    }
}
