<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidReturnType;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\WhenFunctionMissing;
use function end;
use function explode;
use Iterator;
use function method_exists;

abstract class AbstractAggregateRoot
{
    protected ImmutableState $state;

    /**
     * Current version.
     */
    private AggregateVersion $version;

    /**
     * List of events that are not committed to the EventStore.
     *
     * @var DomainEvent[]
     */
    private array $recordedEvents = [];

    /**
     * @internal Don't use it, only when you exactly know what you do
     *
     * @param Iterator<EventEnvelope> $historyEvents History Events
     */
    final public static function reconstituteFromHistory(
        Iterator $historyEvents
    ): static {
        $instance = new static();
        $instance->replay($historyEvents);

        return $instance;
    }

    /**
     * @internal Don't use it, only when you exactly know what you do
     *
     * @param ImmutableState   $state   State
     * @param AggregateVersion $version Version
     */
    final public static function reconstituteFromAggregateState(
        ImmutableState $state,
        AggregateVersion $version
    ): static {
        $instance = new static();
        $instance->version = $version;
        $instance->state = $state;

        return $instance;
    }

    /**
     * Use named constructors to instantiate a new Aggregate.
     *
     * @example User::register()
     */
    final protected function __construct()
    {
        $this->version = AggregateVersion::zero();
    }

    abstract public function aggregateId(): AggregateId;

    final public function state(): ImmutableState
    {
        return $this->state;
    }

    final public function version(): AggregateVersion
    {
        return $this->version;
    }

    /**
     * Get pending events and reset stack.
     *
     * @return DomainEvent[]
     */
    final public function popRecordedEvents(): array
    {
        $pendingEvents = $this->recordedEvents;
        $this->recordedEvents = [];

        return $pendingEvents;
    }

    /**
     * @internal Don't use it, only when you exactly know what you do
     *
     * Replay past events
     *
     * @param Iterator<EventEnvelope> $historyEvents History Events
     */
    final public function replay(Iterator $historyEvents): void
    {
        foreach ($historyEvents as $pastEvent) {
            $this->apply($pastEvent->event());
            $this->version = $pastEvent->aggregateVersion();
        }
    }

    /**
     * Record a domain event.
     *
     * @param DomainEvent $event Domain Event
     */
    protected function recordThat(DomainEvent $event): void
    {
        $this->apply($event);
        $this->version = $this->version->increase();

        $this->recordedEvents[] = $event;
    }

    /**
     * @param DomainEvent $event Domain Event
     */
    protected function nameOf(DomainEvent $event): string
    {
        $parts = explode('\\', \get_class($event));

        return end($parts);
    }

    /**
     * Apply given event.
     *
     * @param DomainEvent $event Domain Event
     */
    private function apply(DomainEvent $event): void
    {
        $eventName = $this->nameOf($event);
        $whenFunc = 'when'.$eventName;

        if (!method_exists($this, $whenFunc)) {
            throw WhenFunctionMissing::forEventWithName($this->nameOf($event), __CLASS__);
        }

        $newState = $this->{$whenFunc}($event);

        if (!$newState instanceof ImmutableState) {
            InvalidReturnType::ofWhenFunction(
                __CLASS__,
                $whenFunc,
                $newState
            );
        }

        $this->state = $newState;
    }
}
