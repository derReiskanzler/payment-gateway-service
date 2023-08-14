<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Currency;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    public function testFromString(): void
    {
        $currency = Currency::fromString('eur');

        $this->assertInstanceOf(
            Currency::class,
            $currency,
            'created currency from string does not match expected class: Currency.'
        );
    }

    public function testToString(): void
    {
        $string = 'eur';
        $currency = Currency::fromString($string);

        $this->assertEquals(
            $string,
            $currency->toString(),
            'currency to string does not match expected string.',
        );
    }
}
