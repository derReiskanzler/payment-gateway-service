<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Test\Extenders\Constraint;

use DateTimeImmutable;
use EventEngine\Messaging\GenericEvent;
use PHPUnit\Framework\ExpectationFailedException;
use Tests\doubles\EventSourcing\UserRegisteredWithOccurredAtDouble;
use Tests\PHPUnit\Extenders\InteractsWithEventStore;
use Tests\TestCase;

final class EventOnStreamTest extends TestCase
{
    use InteractsWithEventStore;

    private string $streamName = 'boilerplate-user-stream';
    private string $userId = '52255543-248e-42ae-adbf-490d6ed8d855';
    private string $aggregateType = 'AppTest.User';

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventStore()->createStream($this->streamName);
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        parent::setUp();
        $this->eventStore()->deleteStream($this->streamName);
    }

    public function testAssertEventOnStream(): void
    {
        $this->givenPersistedEvents();
        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'Jane Doe',
            ],
            [
                'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 1,
            ],
            $skip = 0
        );

        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'Jane Doe',
                'occurred_at' => '2020-06-06T10:00:05.192019',
            ],
            [
                'event_id' => 'f70e3851-0473-489a-b9dd-b3d5bb8136d8',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 2,
            ],
            $skip = 1
        );
    }

    public function testAssertEventOnStreamFailsWhenEventNotFound(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/No event recorded with name AppTest\.UserRegistered/');
        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'Jane Doe',
            ],
            [
                'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 1,
            ],
        );
    }

    public function testAssertEventOnStreamFailsWhenEventNotFoundAfterPosition(): void
    {
        $this->givenPersistedEvents();

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/No event recorded with name AppTest\.UserRegistered/');

        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'Jane Doe',
            ],
            [
                'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 1,
            ],
            $skip = 2
        );
    }

    public function testAssertEventOnStreamFailsWhenPayloadDoesNotMatch(): void
    {
        $this->givenPersistedEvents();

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/The expected payload "name => "does not match"" does not match with the persisted event payload "name => "Jane Doe""\./');

        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'does not match',
            ],
            [
                'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 1,
            ],
        );
    }

    public function testAssertEventOnStreamFailsWhenExpectedPayloadKeyMissing(): void
    {
        $this->givenPersistedEvents();

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/The key "phone" is not present in the event payload\.\./');

        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'Jane Doe',
                'phone' => '+38123123',
            ],
            [
                'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 1,
            ],
        );
    }

    public function testAssertEventOnStreamFailsWhenMetadataDoesNotMatch(): void
    {
        $this->givenPersistedEvents();

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/The expected metadata "_aggregate_version => 2" does not match with the persisted event metadata "_aggregate_version => 1"\./');

        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'Jane Doe',
            ],
            [
                'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 2,
            ],
        );
    }

    public function testAssertEventOnStreamFailsWhenExpectedMetadataKeyMissing(): void
    {
        $this->givenPersistedEvents();

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/The key "_causation_id" is not present in the event metadata\.\./');

        $this->assertEventOnStream(
            $this->streamName,
            $this->aggregateType,
            $this->userId,
            UserRegisteredWithOccurredAtDouble::eventName(),
            [
                'user_id' => $this->userId,
                'name' => 'Jane Doe',
            ],
            [
                'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                '_aggregate_type' => $this->aggregateType,
                '_aggregate_id' => $this->userId,
                '_aggregate_version' => 1,
                '_causation_id' => 'ckyyn8rfq00003p14v3ey1anb',
            ],
        );
    }

    private function givenPersistedEvents(): void
    {
        /** @var GenericEvent[] $genericEvents */
        $genericEvents = [
            GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                'payload' => [
                    'user_id' => $this->userId,
                    'name' => 'Jane Doe',
                ],
                'metadata' => [
                    'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 1,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]),
            GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => '51b4599a-5f76-4063-b088-9d45eb0a3e17',
                'payload' => [
                    'user_id' => $this->userId,
                    'name' => 'Jane Doe',
                    'occurred_at' => '2020-06-06T10:00:05.192019',
                ],
                'metadata' => [
                    'event_id' => 'f70e3851-0473-489a-b9dd-b3d5bb8136d8',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 2,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]),
        ];

        $this->eventStore()->appendTo($this->streamName, ...$genericEvents);
    }
}
