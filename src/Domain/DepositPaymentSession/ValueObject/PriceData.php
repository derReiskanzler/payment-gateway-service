<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class PriceData
{
    public const CURRENCY = 'currency';
    public const UNIT_AMOUNT = 'unit_amount';
    public const PRODUCT_DATA = 'product_data';

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $currency = Currency::fromString($data[self::CURRENCY]);
        $unitAmount = UnitAmount::fromFloat($data[self::UNIT_AMOUNT]);
        $productData = ProductData::fromArray([
            ProductData::NAME => $data[self::PRODUCT_DATA][ProductData::NAME],
            ProductData::IMAGES => $data[self::PRODUCT_DATA][ProductData::IMAGES],
        ]);

        return new self(
            $currency,
            $unitAmount,
            $productData,
        );
    }

    public static function fromValueObjects(
        Currency $currency,
        UnitAmount $unitAmount,
        ProductData $productData,
    ): self {
        return new self(
            $currency,
            $unitAmount,
            $productData,
        );
    }

    private function __construct(
        private Currency $currency,
        private UnitAmount $unitAmount,
        private ProductData $productData
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::CURRENCY => $this->currency->toString(),
            self::UNIT_AMOUNT => $this->unitAmount->toFloat(),
            self::PRODUCT_DATA => $this->productData->toArray(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayAndInCents(): array
    {
        return [
            self::CURRENCY => $this->currency->toString(),
            self::UNIT_AMOUNT => $this->unitAmount->toCents(),
            self::PRODUCT_DATA => $this->productData->toArray(),
        ];
    }
}
