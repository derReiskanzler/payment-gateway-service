<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class LineItemCollection
{
    /**
     * @var LineItem[]
     */
    private array $lineItems;

    public function __construct(LineItem ...$lineItems)
    {
        if (empty($lineItems)) {
            $this->lineItems = [];
        } else {
            foreach ($lineItems as $lineItem) {
                $this->lineItems[] = $lineItem;
            }
        }
    }

    /**
     * @param array<int, mixed> $lineItems
     */
    public static function fromArray(array $lineItems): self
    {
        return new self(
            ...array_map(
                static function (array $lineItem) {
                    return LineItem::fromArray($lineItem);
                },
                array_values($lineItems)
            )
        );
    }

    public static function fromLineItems(LineItem ...$lineItems): self
    {
        return new self(
            ...$lineItems
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        return array_map(
            function (LineItem $lineItem) {
                return $lineItem->toArray();
            },
            $this->lineItems
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function toArrayAndInCents(): array
    {
        return array_map(
            function (LineItem $lineItem) {
                return $lineItem->toArrayAndInCents();
            },
            $this->lineItems
        );
    }

    public function add(LineItem $lineItem): self
    {
        $copy = clone $this;
        $copy->lineItems[] = $lineItem;

        return $copy;
    }
}
