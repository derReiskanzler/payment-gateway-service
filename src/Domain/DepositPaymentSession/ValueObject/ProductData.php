<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class ProductData
{
    public const NAME = 'name';
    public const IMAGES = 'images';

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $name = ProductName::fromString($data[self::NAME]);

        return new self($name, $data[self::IMAGES]);
    }

    /**
     * @param string[] $images
     */
    public static function withValueObjects(ProductName $name, array $images = []): self
    {
        return new self($name, $images);
    }

    /**
     * @param string[] $images
     */
    private function __construct(
        private ProductName $name,
        private ?array $images = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::NAME => $this->name->toString(),
            self::IMAGES => $this->images,
        ];
    }
}
