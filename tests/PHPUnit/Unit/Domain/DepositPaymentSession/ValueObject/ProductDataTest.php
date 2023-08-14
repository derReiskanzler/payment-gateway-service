<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductData;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ProductName;
use Generator;
use PHPUnit\Framework\TestCase;

final class ProductDataTest extends TestCase
{
    /**
     * @param array<string, mixed> $productDataArray
     * @dataProvider provideProductData
     */
    public function testFromArray(array $productDataArray): void
    {
        $productData = ProductData::fromArray($productDataArray);

        $this->assertInstanceOf(
            ProductData::class,
            $productData,
            'created product data from array does not match expected class: ProductData.'
        );
    }

    /**
     * @param array<string, mixed> $productDataArray
     * @dataProvider provideProductData
     */
    public function testWithValueObjects(array $productDataArray): void
    {
        $productData = ProductData::withValueObjects(
            ProductName::fromString($productDataArray['name']),
            $productDataArray['images']
        );

        $this->assertInstanceOf(
            ProductData::class,
            $productData,
            'created product data from array does not match expected class: ProductData.'
        );
    }

    /**
     * @param array<string, mixed> $productDataArray
     * @dataProvider provideProductData
     */
    public function testToArray(array $productDataArray): void
    {
        $productData = ProductData::fromArray($productDataArray);

        $this->assertEquals(
            $productDataArray,
            $productData->toArray(),
            'product data to array does not match expected array.',
        );
    }

    public function provideProductData(): Generator
    {
        yield 'ProductData array' => [
            'product data array' => [
                ProductData::NAME => 'WE 1',
                ProductData::IMAGES => ['https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl/'],
            ],
        ];
    }
}
