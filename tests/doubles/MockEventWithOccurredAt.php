<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ImmutableEventTrait;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock\Clock;
use DateTimeImmutable;

class MockEventWithOccurredAt implements DomainEvent
{
    use ImmutableEventTrait;

    private string $expectedTime;

    private ?string $occurred_at = null;

    public static function eventName(): string
    {
        return 'FooEvent';
    }

    public function expectedTime(): string
    {
        return $this->expectedTime;
    }

    public function occurred_at(): string
    {
        return $this->occurred_at;
    }

    private function provideClock(): Clock
    {
        return new class($this->expectedTime) implements Clock {
            private string $time;

            /**
             * @param string $time time
             */
            public function __construct(string $time)
            {
                $this->time = $time;
            }

            public function now(): DateTimeImmutable
            {
                return DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u', $this->time);
            }
        };
    }
}
