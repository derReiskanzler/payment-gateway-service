<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock\Clock;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock\SystemClock;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\ImmutableRecordLogicTrait;

trait ImmutableEventTrait
{
    use ImmutableRecordLogicTrait {
        toArray as originalToArray;
    }

    /**
     * @internal property
     *
     * If not set by the domain, ImmutableEventTrait::toArray() set it to now
     */
    private ?string $occurredAt = null;

    /**
     * @internal property
     *
     * If not set by the domain, ImmutableEventTrait::toArray() set it to now
     *
     * @deprecated variable $occurredAt should be used. this one is kept as backward compatible solution for using EventPayloadTranslator
     */
    private ?string $occurred_at = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $original = $this->originalToArray();
        if ($this->checkIfOccurredAtExistsInOriginalPayload($original)) {
            /*
             * won't be needed after sunset of EventPayloadTranslator
             */
            unset($original['occurredAt']);

            return $original;
        }

        return $this->addOccurredAt($original);
    }

    public function occurredAt(): ?string
    {
        return $this->occurredAt;
    }

    /**
     * @deprecated method occurredAt() should be used. this one is kept as backward compatible solution for using EventPayloadTranslator
     */
    public function occurred_at(): ?string
    {
        return $this->occurred_at;
    }

    /**
     * @param array<string, string> $payload payload
     *
     * @return array<string, string>
     */
    private function addOccurredAt(array $payload): array
    {
        $clock = $this->provideClock();

        /*
         * won't be needed after sunset of EventPayloadTranslator
         */
        unset($payload['occurredAt']);
        $payload['occurred_at'] = $clock->now()->format('Y-m-d\TH:i:s.u');

        return $payload;
    }

    private function provideClock(): Clock
    {
        return new SystemClock();
    }

    /**
     * @param array<string, string> $original
     */
    private function checkIfOccurredAtExistsInOriginalPayload(array $original): bool
    {
        return
            (\array_key_exists('occurred_at', $original) && null !== $original['occurred_at'])
            || (\array_key_exists('occurredAt', $original) && null !== $original['occurredAt'])
        ;
    }
}
