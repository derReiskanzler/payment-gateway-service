<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class LineItem
{
    public const QUANTITY = 'quantity';
    public const PRICE_DATA = 'price_data';

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $quantity = Quantity::fromInt($data[self::QUANTITY]);
        $priceData = PriceData::fromArray([
            PriceData::CURRENCY => $data[self::PRICE_DATA][PriceData::CURRENCY],
            PriceData::UNIT_AMOUNT => $data[self::PRICE_DATA][PriceData::UNIT_AMOUNT],
            PriceData::PRODUCT_DATA => [
                ProductData::NAME => $data[self::PRICE_DATA][PriceData::PRODUCT_DATA][ProductData::NAME],
                ProductData::IMAGES => $data[self::PRICE_DATA][PriceData::PRODUCT_DATA][ProductData::IMAGES],
            ],
        ]);

        return new self(
            $quantity,
            $priceData
        );
    }

    public static function fromValueObjects(Quantity $quantity, PriceData $priceData): self
    {
        return new self(
            $quantity,
            $priceData
        );
    }

    private function __construct(
        private Quantity $quantity,
        private PriceData $priceData,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::QUANTITY => $this->quantity->toInt(),
            self::PRICE_DATA => $this->priceData->toArray(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayAndInCents(): array
    {
        return [
            self::QUANTITY => $this->quantity->toInt(),
            self::PRICE_DATA => $this->priceData->toArrayAndInCents(),
        ];
    }
}
