<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    public function testFromInt(): void
    {
        $quantity = Quantity::fromInt(42);

        $this->assertInstanceOf(
            Quantity::class,
            $quantity,
            'created quantity from string does not match expected class: Quantity.',
        );
    }

    public function testToInt(): void
    {
        $int = 42;
        $quantity = Quantity::fromInt($int);

        $this->assertEquals(
            $int,
            $quantity->toInt(),
            'quantity to int does not match expected int.',
        );
    }
}
