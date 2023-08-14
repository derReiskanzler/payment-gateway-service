<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Currency;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PriceData;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductData;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductName;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitAmount;
use Generator;
use PHPUnit\Framework\TestCase;

final class PriceDataTest extends TestCase
{
    /**
     * @param array<string, mixed> $priceDataArray
     * @dataProvider providePriceData
     */
    public function testFromArray(array $priceDataArray): void
    {
        $priceData = PriceData::fromArray($priceDataArray);

        $this->assertInstanceOf(
            PriceData::class,
            $priceData,
            'created price data from array does not match expected class: PriceData.'
        );
    }

    /**
     * @param array<string, mixed> $priceDataArray
     * @dataProvider providePriceData
     */
    public function testFromValueObjects(array $priceDataArray): void
    {
        $priceData = PriceData::fromValueObjects(
            Currency::fromString($priceDataArray['currency']),
            UnitAmount::fromFloat($priceDataArray['unit_amount']),
            ProductData::withValueObjects(
                ProductName::fromString($priceDataArray['product_data']['name']),
                $priceDataArray['product_data']['images']
            ),
        );

        $this->assertInstanceOf(
            PriceData::class,
            $priceData,
            'created price data from value objects does not match expected class: PriceData.'
        );
    }

    /**
     * @param array<string, mixed> $priceDataArray
     * @dataProvider providePriceData
     */
    public function testToArray(array $priceDataArray): void
    {
        $priceData = PriceData::fromArray($priceDataArray);

        $this->assertEquals(
            $priceDataArray,
            $priceData->toArray(),
            'price data to array does not match expected array.',
        );
    }

    /**
     * @param array<string, mixed> $priceDataArray
     * @param array<string, mixed> $priceDataArrayAndInCents
     * @dataProvider providePriceData
     */
    public function testToArrayAndInCents(array $priceDataArray, array $priceDataArrayAndInCents): void
    {
        $priceData = PriceData::fromArray($priceDataArray);

        $this->assertEquals(
            $priceDataArrayAndInCents,
            $priceData->toArrayAndInCents(),
            'price data to array and in cents does not match expected array.',
        );
    }

    public function providePriceData(): Generator
    {
        yield 'PriceData array' => [
            'price data' => [
                PriceData::CURRENCY => 'eur',
                PriceData::UNIT_AMOUNT => 3000.00,
                PriceData::PRODUCT_DATA => [
                    ProductData::NAME => 'WE 1',
                    ProductData::IMAGES => ['https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl/'],
                ],
            ],
            'price data in cents' => [
                PriceData::CURRENCY => 'eur',
                PriceData::UNIT_AMOUNT => 300000.00,
                PriceData::PRODUCT_DATA => [
                    ProductData::NAME => 'WE 1',
                    ProductData::IMAGES => ['https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl/'],
                ],
            ],
        ];
    }
}
