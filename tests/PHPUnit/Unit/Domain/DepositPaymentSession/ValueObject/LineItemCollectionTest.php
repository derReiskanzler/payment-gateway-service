<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\LineItem;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\LineItemCollection;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PriceData;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductData;
use Generator;
use PHPUnit\Framework\TestCase;

final class LineItemCollectionTest extends TestCase
{
    /**
     * @param array<string, mixed> $lineItemArray1
     * @param array<string, mixed> $lineItemArray2
     * @dataProvider provideLineItems
     */
    public function testFromArray(array $lineItemArray1, array $lineItemArray2): void
    {
        $lineItemCollection = LineItemCollection::fromArray([
            $lineItemArray1,
            $lineItemArray2,
        ]);

        $this->assertInstanceOf(
            LineItemCollection::class,
            $lineItemCollection,
            'created line item collection from array does not match expected class: LineItemCollection.'
        );
    }

    /**
     * @param array<string, mixed> $lineItemArray1
     * @param array<string, mixed> $lineItemArray2
     * @dataProvider provideLineItems
     */
    public function testFromLineItems(array $lineItemArray1, array $lineItemArray2): void
    {
        $lineItemCollection = LineItemCollection::fromLineItems(
            LineItem::fromArray($lineItemArray1),
            LineItem::fromArray($lineItemArray2),
        );

        $this->assertInstanceOf(
            LineItemCollection::class,
            $lineItemCollection,
            'created line item collection from line items does not match expected class: LineItemCollection.'
        );
    }

    /**
     * @param array<string, mixed> $lineItemArray1
     * @param array<string, mixed> $lineItemArray2
     * @dataProvider provideLineItems
     */
    public function testToArray(array $lineItemArray1, array $lineItemArray2): void
    {
        $lineItemCollection = LineItemCollection::fromArray([
            $lineItemArray1,
            $lineItemArray2,
        ]);

        $this->assertEquals(
            [
                0 => $lineItemArray1,
                1 => $lineItemArray2,
            ],
            $lineItemCollection->toArray(),
            'lineItem collection to array does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $lineItemArray1
     * @param array<string, mixed> $lineItemArray2
     * @dataProvider provideLineItems
     */
    public function testToArrayAndInCents(array $lineItemArray1, array $lineItemArray2): void
    {
        $lineItem1 = LineItem::fromArray($lineItemArray1);
        $lineItem2 = LineItem::fromArray($lineItemArray2);

        $lineItemCollection = LineItemCollection::fromLineItems(
            $lineItem1,
            $lineItem2,
        );

        $this->assertEquals(
            [
                0 => $lineItem1->toArrayAndInCents(),
                1 => $lineItem2->toArrayAndInCents(),
            ],
            $lineItemCollection->toArrayAndInCents(),
            'lineItem collection to array does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $lineItemArray1
     * @param array<string, mixed> $lineItemArray2
     * @param array<string, mixed> $lineItemArray3
     * @dataProvider provideLineItems
     */
    public function testAdd(array $lineItemArray1, array $lineItemArray2, array $lineItemArray3): void
    {
        $lineItemCollection = LineItemCollection::fromArray([
            $lineItemArray1,
            $lineItemArray2,
        ]);

        $lineItemCollection = $lineItemCollection->add(
            LineItem::fromArray($lineItemArray3)
        );

        $this->assertEquals(
            [
                0 => $lineItemArray1,
                1 => $lineItemArray2,
                2 => $lineItemArray3,
            ],
            $lineItemCollection->toArray(),
            'line item collection after adding a line item does not match expected array.'
        );
    }

    public function provideLineItems(): Generator
    {
        yield 'LineItemCollection data' => [
            'Line Item 1 array' => [
                LineItem::QUANTITY => 1,
                    LineItem::PRICE_DATA => [
                        PriceData::CURRENCY => 'eur',
                        PriceData::UNIT_AMOUNT => 3000.00,
                        PriceData::PRODUCT_DATA => [
                        ProductData::NAME => 'WE 1',
                        ProductData::IMAGES => ['https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl/'],
                    ],
                ],
            ],
            'Line Item 2 array' => [
                LineItem::QUANTITY => 1,
                LineItem::PRICE_DATA => [
                    PriceData::CURRENCY => 'eur',
                    PriceData::UNIT_AMOUNT => 4000.00,
                    PriceData::PRODUCT_DATA => [
                        ProductData::NAME => 'WE 2',
                        ProductData::IMAGES => [],
                    ],
                ],
            ],
            'Line Item 3 array' => [
                LineItem::QUANTITY => 1,
                LineItem::PRICE_DATA => [
                    PriceData::CURRENCY => 'eur',
                    PriceData::UNIT_AMOUNT => 5000.00,
                    PriceData::PRODUCT_DATA => [
                        ProductData::NAME => 'WE 3',
                        ProductData::IMAGES => null,
                    ],
                ],
            ],
        ];
    }
}
