<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use PHPUnit\Framework\TestCase;

final class CheckoutSessionIdTest extends TestCase
{
    public function testFromString(): void
    {
        $checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2');

        $this->assertInstanceOf(
            CheckoutSessionId::class,
            $checkoutSessionId,
            'created checkout session id from string does not match expected class: CheckoutSessionId.'
        );
    }

    public function testToString(): void
    {
        $string = 'cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2';
        $checkoutSessionId = CheckoutSessionId::fromString($string);

        $this->assertEquals(
            $string,
            $checkoutSessionId->toString(),
            'checkout session id to string does not match expected string.',
        );
    }
}
