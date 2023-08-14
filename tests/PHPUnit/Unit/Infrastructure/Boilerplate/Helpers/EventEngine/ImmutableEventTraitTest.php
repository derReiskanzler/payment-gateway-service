<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventEngine;

use Exception;
use PHPUnit\Framework\TestCase;
use Tests\doubles\MockEvent;
use Tests\doubles\MockEventWithOccurredAt;

class ImmutableEventTraitTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAddOccurredAtToEventPayload(): void
    {
        $event = MockEvent::fromRecordData(['expectedTime' => '2020-01-01T09:16:18.964957']);

        $expectedEventPayload = [
            'expectedTime' => '2020-01-01T09:16:18.964957',
            'occurred_at' => '2020-01-01T09:16:18.964957',
        ];

        static::assertSame($expectedEventPayload, $event->toArray());
    }

    /**
     * @throws Exception
     */
    public function testOccurredAtWillNotBeOverwrittenWhenAlreadySet(): void
    {
        $event = MockEventWithOccurredAt::fromRecordData([
            'expectedTime' => '2020-01-01T09:16:18.964957',
            'occurred_at' => '2019-01-01T09:16:18.964957',
        ]);

        $expectedEventPayload = [
            'expectedTime' => '2020-01-01T09:16:18.964957',
            'occurred_at' => '2019-01-01T09:16:18.964957',
        ];

        static::assertSame($expectedEventPayload, $event->toArray());
    }
}
