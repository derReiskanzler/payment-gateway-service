<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventSourcing\Event;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidEventNameFormat;
use PHPUnit\Framework\TestCase;

class EventNameTest extends TestCase
{
    public function testEventNameEnsuresContextIsGiven(): void
    {
        $eventName = EventName::fromString('Context.SomethingHappened');

        static::assertSame('Context.SomethingHappened', $eventName->toString());

        $this->expectException(InvalidEventNameFormat::class);

        EventName::fromString('SomethingHappenedWithoutContext');
    }
}
