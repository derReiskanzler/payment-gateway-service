<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use PHPUnit\Framework\TestCase;

class AggregateVersionTest extends TestCase
{
    public function testAggregateVersionCanBeIncreased(): void
    {
        $aggregateVersion = AggregateVersion::zero();

        static::assertSame(0, $aggregateVersion->toInt());

        $aggregateVersionPlusOne = $aggregateVersion->increase();

        static::assertSame(0, $aggregateVersion->toInt());
        static::assertSame(1, $aggregateVersionPlusOne->toInt());
    }

    public function testAggregateVersionAllowsTypeCastToString(): void
    {
        $aggregateVersion = AggregateVersion::fromInt(10);

        static::assertSame('10', (string) $aggregateVersion);
    }
}
