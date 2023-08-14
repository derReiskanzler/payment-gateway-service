<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductName;
use PHPUnit\Framework\TestCase;

final class ProductNameTest extends TestCase
{
    public function testFromString(): void
    {
        $productName = ProductName::fromString('WE 01');

        $this->assertInstanceOf(
            ProductName::class,
            $productName,
            'created product name from string does not match expected class: ProductName.',
        );
    }

    public function testToString(): void
    {
        $productNameString = 'WE 01';
        $productName = ProductName::fromString($productNameString);

        $this->assertEquals(
            $productNameString,
            $productName->toString(),
            'product name to string does not match expected string.',
        );
    }
}
