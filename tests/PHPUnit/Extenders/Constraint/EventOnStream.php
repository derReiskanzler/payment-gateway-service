<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Extenders\Constraint;

use function array_keys;
use EventEngine\EventStore\EventStore;
use EventEngine\Messaging\GenericEvent;
use function json_encode;
use const JSON_PRETTY_PRINT;
use JsonException;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use function sprintf;
use Webmozart\Assert\Assert;

final class EventOnStream extends Constraint
{
    private string $additionalFailureDescription = '';

    /**
     * Check that a given event was persisted on stream.
     *
     * @param string            $aggregateType    Aggregate Type
     * @param string            $aggregateId      Aggregate Id
     * @param string            $eventName        Event Name
     * @param array<mixed>|null $expectedPayload  Optional Payload to check
     * @param array<mixed>|null $expectedMetadata Optional Metadata to check
     * @param int               $skip             Skip number of events with same name
     */
    public function __construct(
        private EventStore $eventStore,
        private string $aggregateType,
        private string $aggregateId,
        private string $eventName,
        private ?array $expectedPayload = null,
        private ?array $expectedMetadata = null,
        private int $skip = 0
    ) {
        if (null !== $this->expectedPayload) {
            Assert::allValidArrayKey(array_keys($this->expectedPayload));
        }
        if (null !== $this->expectedMetadata) {
            Assert::allValidArrayKey(array_keys($this->expectedMetadata));
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws JsonException
     */
    public function toString(): string
    {
        return json_encode([
            'aggregate_type' => $this->aggregateType,
            'aggregate_id' => $this->aggregateId,
            'event_name' => $this->eventName,
            'payload' => $this->expectedPayload,
            'metadata' => $this->expectedMetadata,
        ], \JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param string $streamName stream name
     *
     * @throws JsonException
     */
    protected function matches($streamName): bool
    {
        $foundEvents = $this->eventStore->loadAggregateEvents(
            $streamName,
            $this->aggregateType,
            $this->aggregateId
        );

        $eventFound = false;

        $i = 0;

        /** @var GenericEvent $event */
        foreach ($foundEvents as $event) {
            if ($event->messageName() === $this->eventName) {
                if ($i !== $this->skip) {
                    ++$i;
                    continue;
                }
                $eventFound = true;

                $this->evaluatePayload($event);
                $this->evaluateMetadata($event);

                break;
            }
        }

        if (!$eventFound) {
            $this->additionalFailureDescription .= sprintf("\n'No event recorded with name %s'", $this->eventName);
        }

        return $eventFound && empty($this->additionalFailureDescription);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * To provide additional failure information additionalFailureDescription
     * can be used.
     *
     * @param mixed $streamName evaluated value or object
     *
     * @throws InvalidArgumentException
     * @throws JsonException
     */
    protected function failureDescription($streamName): string
    {
        return sprintf(
            "an event in the stream [%s] matches the attributes %s after position %d.\n\n%s",
            $streamName,
            $this->toString(),
            $this->skip,
            $this->getAdditionalInfo()
        );
    }

    private function getAdditionalInfo(): string
    {
        return sprintf("Additional information:\n%s", $this->additionalFailureDescription);
    }

    private function evaluateMetadata(GenericEvent $event): void
    {
        if ($this->expectedMetadata) {
            $actualMetadata = $event->metadata();
            foreach ($this->expectedMetadata as $expectedKey => $expectedValue) {
                if (!(new ArrayHasKey($expectedKey))->evaluate($actualMetadata, '', true)) {
                    $this->additionalFailureDescription .= sprintf(
                        "\nThe key \"%s\" is not present in the event metadata.",
                        $expectedKey,
                    );

                    continue;
                }
                if (!(new IsEqual($expectedValue))->evaluate($actualMetadata[$expectedKey], '', true)) {
                    $this->additionalFailureDescription .= sprintf(
                        "\nThe expected metadata \"%s => %s\" does not match with the persisted event metadata \"%s => %s\"",
                        $expectedKey,
                        json_encode($expectedValue, flags: \JSON_THROW_ON_ERROR),
                        $expectedKey,
                        json_encode($actualMetadata[$expectedKey], flags: \JSON_THROW_ON_ERROR)
                    );
                }
            }
        }
    }

    private function evaluatePayload(GenericEvent $event): void
    {
        if ($this->expectedPayload) {
            $actualPayload = $event->payload();
            foreach ($this->expectedPayload as $expectedKey => $expectedValue) {
                if (!(new ArrayHasKey($expectedKey))->evaluate($actualPayload, '', true)) {
                    $this->additionalFailureDescription .= sprintf(
                        "\nThe key \"%s\" is not present in the event payload.",
                        $expectedKey,
                    );

                    continue;
                }
                if (!(new IsEqual($expectedValue))->evaluate($actualPayload[$expectedKey], '', true)) {
                    $this->additionalFailureDescription .= sprintf(
                        "\nThe expected payload \"%s => %s\" does not match with the persisted event payload \"%s => %s\"",
                        $expectedKey,
                        json_encode($expectedValue, flags: \JSON_THROW_ON_ERROR),
                        $expectedKey,
                        json_encode($actualPayload[$expectedKey], flags: \JSON_THROW_ON_ERROR)
                    );
                }
            }
        }
    }
}
