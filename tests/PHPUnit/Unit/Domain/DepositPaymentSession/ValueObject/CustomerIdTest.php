<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CustomerId;
use PHPUnit\Framework\TestCase;

final class CustomerIdTest extends TestCase
{
    public function testFromString(): void
    {
        $customerId = CustomerId::fromString('customer id');

        $this->assertInstanceOf(
            CustomerId::class,
            $customerId,
            'created customer id from string does not match expected class: CustomerId.'
        );
    }

    public function testToString(): void
    {
        $string = 'customer id';
        $customerId = CustomerId::fromString($string);

        $this->assertEquals(
            $string,
            $customerId->toString(),
            'customer id to string does not match expected string.',
        );
    }
}
