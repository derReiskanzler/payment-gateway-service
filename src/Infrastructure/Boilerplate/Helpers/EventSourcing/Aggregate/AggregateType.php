<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidAggregateTypeFormat;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;
use function explode;

final class AggregateType
{
    use toString;

    private string $context;

    private string $type;

    /**
     * @throws InvalidAggregateTypeFormat if the aggregateType is not dot-separated
     */
    public static function fromString(string $aggregateType): self
    {
        return new self($aggregateType);
    }

    /**
     * @throws InvalidAggregateTypeFormat if the aggregateType is not dot-separated
     *
     * @return array<int, string>
     */
    private static function split(string $aggregateType): array
    {
        $parts = explode('.', $aggregateType);

        if (2 !== \count($parts)) {
            throw InvalidAggregateTypeFormat::notDotSeparated($aggregateType);
        }

        return $parts;
    }

    /**
     * @throws InvalidAggregateTypeFormat if the aggregateType is not dot-separated
     */
    private function __construct(string $aggregateType)
    {
        [$this->context, $this->type] = self::split($aggregateType);
    }

    public function toString(): string
    {
        return $this->context.'.'.$this->type;
    }
}
