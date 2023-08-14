<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Currency;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\LineItem;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PriceData;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductData;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductName;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Quantity;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitAmount;
use Generator;
use PHPUnit\Framework\TestCase;

final class LineItemTest extends TestCase
{
    /**
     * @param array<string, mixed> $lineItemArray
     * @dataProvider provideLineItemData
     */
    public function testFromArray(array $lineItemArray): void
    {
        $lineItem = LineItem::fromArray($lineItemArray);

        $this->assertInstanceOf(
            LineItem::class,
            $lineItem,
            'created line item from array does not match expected class: LineItem.',
        );
    }

    /**
     * @param array<string, mixed> $lineItemArray
     * @dataProvider provideLineItemData
     */
    public function testFromValueObjects(array $lineItemArray): void
    {
        $lineItem = LineItem::fromValueObjects(
            Quantity::fromInt($lineItemArray['quantity']),
            PriceData::fromValueObjects(
                Currency::fromString($lineItemArray['price_data']['currency']),
                UnitAmount::fromFloat($lineItemArray['price_data']['unit_amount']),
                ProductData::withValueObjects(
                    ProductName::fromString($lineItemArray['price_data']['product_data']['name']),
                ),
            ),
        );

        $this->assertInstanceOf(
            LineItem::class,
            $lineItem,
            'created line item from value objects does not match expected class: LineItem.',
        );
    }

    /**
     * @param array<string, mixed> $lineItemArray
     * @dataProvider provideLineItemData
     */
    public function testToArray(array $lineItemArray): void
    {
        $lineItem = LineItem::fromArray($lineItemArray);

        $this->assertEquals(
            $lineItemArray,
            $lineItem->toArray(),
            'line item to array does not match expected array.',
        );
    }

    /**
     * @param array<string, mixed> $lineItemArray
     * @param array<string, mixed> $lineItemArrayInCents
     * @dataProvider provideLineItemData
     */
    public function testToArrayAndInCents(array $lineItemArray, array $lineItemArrayInCents): void
    {
        $lineItem = LineItem::fromArray($lineItemArray);

        $this->assertEquals(
            $lineItemArrayInCents,
            $lineItem->toArrayAndInCents(),
            'line item to array and in cents does not match expected array.',
        );
    }

    public function provideLineItemData(): Generator
    {
        yield 'LineItem data' => [
            'line item array data' => [
                LineItem::QUANTITY => 1,
                LineItem::PRICE_DATA => [
                    PriceData::CURRENCY => 'eur',
                    PriceData::UNIT_AMOUNT => 3000.0,
                    PriceData::PRODUCT_DATA => [
                        ProductData::NAME => 'WE 1',
                        ProductData::IMAGES => ['https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl/'],
                    ],
                ],
            ],
            'line item array dat in cents' => [
                LineItem::QUANTITY => 1,
                LineItem::PRICE_DATA => [
                    PriceData::CURRENCY => 'eur',
                    PriceData::UNIT_AMOUNT => 300000.0,
                    PriceData::PRODUCT_DATA => [
                        ProductData::NAME => 'WE 1',
                        ProductData::IMAGES => ['https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl/'],
                    ],
                ],
            ],
        ];
    }
}
