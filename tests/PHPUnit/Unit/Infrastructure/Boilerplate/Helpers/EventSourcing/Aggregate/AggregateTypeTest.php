<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateType;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidAggregateTypeFormat;
use PHPUnit\Framework\TestCase;

class AggregateTypeTest extends TestCase
{
    public function testAggregateTypeEnsuresContextIsGiven(): void
    {
        $aggregateType = AggregateType::fromString('SomeContext.User');

        static::assertSame('SomeContext.User', (string) $aggregateType);

        $this->expectException(InvalidAggregateTypeFormat::class);

        AggregateType::fromString('UserWithoutContext');
    }
}
