<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer\DocumentStateTranslatorFactory;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer\DocumentStateTranslatorInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\AbstractAggregateRoot;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateTypeMap;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\AggregateNotFound;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\MetadataEnrichers\DefaultMetadataEnricher;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\MetadataEnrichers\MetadataEnricherInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Events\ProophEventStoredInStream;
use function array_map;
use EventEngine\Messaging\GenericEvent;
use EventEngine\Messaging\Message;
use EventEngine\Persistence\DeletableState;
use EventEngine\Persistence\MultiModelStore;
use EventEngine\Util\MapIterator;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Iterator;
use Throwable;

abstract class MultiModelStoreAggregateRepository
{
    private ContextStreamName $contextStreamName;

    private StateCollectionName $stateCollectionName;

    private AggregateTypeMap $aggregateTypeMap;

    private EventTranslator $eventTranslator;

    private DocumentStateTranslatorInterface $documentStateTranslator;

    /**
     * One of:  MultiModelStore::STORAGE_MODE_*.
     */
    private string $multiStoreMode;

    /**
     * MultiModelStoreAggregateRepository constructor.
     *
     * @param MultiModelStore $multiModelStore Store instance
     *
     * @throws BindingResolutionException if DocumentStateTranslatorInterface is not bound
     */
    public function __construct(
        private MultiModelStore $multiModelStore
    ) {
        $this->contextStreamName = $this->getContextStreamName();
        $this->stateCollectionName = $this->getStateCollectionName();
        $this->aggregateTypeMap = $this->getAggregateTypeMap();
        $this->eventTranslator = $this->getEventTranslatorMap();
        $this->multiStoreMode = $this->getMultiStoreMode();
        $this->documentStateTranslator = $this->getDocumentStateTranslator();
    }

    abstract protected function getStreamName(): string;

    abstract protected function getCollectionName(): string;

    abstract protected function getAggregateTypeMap(): AggregateTypeMap;

    abstract protected function getEventTranslatorMap(): EventTranslator;

    /**
     * Save Aggregate.
     *
     * Method is protected. You should add a public save method type hinting a concrete aggregate class
     * and use the saveAggregate method internally.
     *
     * @param AbstractAggregateRoot $aggregate Aggregate
     * @param Command               $command   Command
     *
     * @throws Throwable
     *
     * @example
     * <code>
     * class UserRepository extends MultiModelStoreAggregateRepository
     * {
     *    public function save(UserBehavior $user, Command $command): void
     *    {
     *       $this->saveAggregate($user, $command);
     *    }
     * }
     * </code>
     */
    protected function saveAggregate(AbstractAggregateRoot $aggregate, Command $command): void
    {
        $pendingEvents = $aggregate->popRecordedEvents();

        if (0 === \count($pendingEvents)) {
            return;
        }

        $eeEvents = $this->translateEvents($aggregate, $command, ...$pendingEvents);

        $this->multiModelStore->connection()->beginTransaction();
        try {
            if (MultiModelStore::STORAGE_MODE_STATE !== $this->multiStoreMode) {
                $this->multiModelStore->appendTo($this->contextStreamName->toString(), ...$eeEvents);
            }
            if (MultiModelStore::STORAGE_MODE_EVENTS !== $this->multiStoreMode) {
                $this->deleteOrPersistAggregateState($aggregate);
            }
            $this->multiModelStore->connection()->commit();
        } catch (Throwable $error) {
            $this->multiModelStore->connection()->rollBack();
            throw $error;
        }

        $this->dispatchEvents($eeEvents);
    }

    /**
     * Get Aggregate by id.
     *
     * Method is protected. You should add a public get method type hinting a concrete aggregate class
     * and use the getAggregate method internally.
     *
     * @throws AggregateNotFound if the aggregate cannot be found
     *
     * @example
     * <code>
     * class UserRepository extends MultiModelStoreAggregateRepository
     * {
     *    public function get(UserId $userId): UserBehavior
     *    {
     *       return $this->getAggregate(AggregateId::fromValueObject($userId));
     *    }
     * }
     * </code>
     */
    protected function getAggregate(AggregateId $aggregateId): AbstractAggregateRoot
    {
        /** @var AbstractAggregateRoot $aggregateBehaviorClass */
        $aggregateBehaviorClass = $this->aggregateTypeMap->aggregateBehaviorClass()->toString();

        if (MultiModelStore::STORAGE_MODE_EVENTS !== $this->multiStoreMode) {
            /** @var array<string, mixed>|null $aggregateStateDoc */
            $aggregateStateDoc = $this->multiModelStore->getDoc(
                $this->stateCollectionName->toString(),
                $aggregateId->toString()
            );

            if ($aggregateStateDoc) {
                /** @var class-string<ImmutableState> $stateClass */
                $stateClass = $this->aggregateTypeMap->aggregateStateClass()->toString();
                $aggregateState = $this->denormalizeAggregateState($stateClass, $aggregateStateDoc['state']);

                return $aggregateBehaviorClass::reconstituteFromAggregateState(
                    $aggregateState,
                    AggregateVersion::fromInt($aggregateStateDoc['version'])
                );
            }
        }

        $streamEvents = $this->multiModelStore->loadAggregateEvents(
            $this->contextStreamName->toString(),
            $this->aggregateTypeMap->aggregateType()->toString(),
            $aggregateId->toString()
        );

        if (!$streamEvents->valid()) {
            throw AggregateNotFound::withId($aggregateId, $this->aggregateTypeMap->aggregateType());
        }

        return $aggregateBehaviorClass::reconstituteFromHistory(new MapIterator(
            $streamEvents,
            fn (GenericEvent $event): EventEnvelope => $this->eventTranslator->translateFromGenericToDomain($event)
        ));
    }

    protected function getAggregateUntilVersion(AggregateId $aggregateId, int $maxVersion): AbstractAggregateRoot
    {
        /** @var AbstractAggregateRoot $aggregateBehaviorClass */
        $aggregateBehaviorClass = $this->aggregateTypeMap->aggregateBehaviorClass()->toString();

        $streamEvents = $this->multiModelStore->loadAggregateEvents(
            $this->contextStreamName->toString(),
            $this->aggregateTypeMap->aggregateType()->toString(),
            $aggregateId->toString(),
            maxVersion: $maxVersion
        );

        if (!$streamEvents->valid()) {
            throw AggregateNotFound::withId($aggregateId, $this->aggregateTypeMap->aggregateType());
        }

        return $aggregateBehaviorClass::reconstituteFromHistory(new MapIterator(
            $streamEvents,
            fn (GenericEvent $event): EventEnvelope => $this->eventTranslator->translateFromGenericToDomain($event)
        ));
    }

    /**
     * @param AggregateId $aggregateId AggregateId
     *
     * @return iterable<MapIterator<Iterator, EventEnvelope>>
     */
    protected function getAggregateEvents(AggregateId $aggregateId): iterable
    {
        $streamEvents = $this->multiModelStore->loadAggregateEvents(
            $this->contextStreamName->toString(),
            $this->aggregateTypeMap->aggregateType()->toString(),
            $aggregateId->toString()
        );

        if (!$streamEvents->valid()) {
            throw AggregateNotFound::withId($aggregateId, $this->aggregateTypeMap->aggregateType());
        }

        return new MapIterator(
            $streamEvents,
            fn (GenericEvent $event): EventEnvelope => $this->eventTranslator->translateFromGenericToDomain($event)
        );
    }

    /**
     * Override the hook to provide aggregate metadata for Document Store metadata columns.
     *
     * @param ImmutableState $state Immutable State
     *
     * @return array<string, string>|null
     */
    protected function deriveAggregateMetadataFromState(ImmutableState $state): ?array
    {
        return null;
    }

    /**
     * @throws BindingResolutionException if DocumentStateTranslatorInterface is not bound
     */
    protected function getDocumentStateTranslator(): DocumentStateTranslatorInterface
    {
        return DocumentStateTranslatorFactory::make();
    }

    /**
     * One of:  MultiModelStore::STORAGE_MODE_*
     * Override the hook to provide different Mode.
     */
    protected function getMultiStoreMode(): string
    {
        return MultiModelStore::STORAGE_MODE_EVENTS_AND_STATE;
    }

    private function getContextStreamName(): ContextStreamName
    {
        return ContextStreamName::fromString($this->getStreamName());
    }

    private function getStateCollectionName(): StateCollectionName
    {
        return StateCollectionName::fromString($this->getCollectionName());
    }

    /**
     * @param AbstractAggregateRoot $aggregate       Aggregate that has recorded the events
     * @param Command               $command         Command that caused recording of the events
     * @param DomainEvent           ...$domainEvents Recorded domain events
     *
     * @throws Exception
     *
     * @return array<int|string, GenericEvent>
     */
    private function translateEvents(
        AbstractAggregateRoot $aggregate,
        Command $command,
        DomainEvent ...$domainEvents
    ): array {
        $currentVersion = $aggregate->version()->subtract(\count($domainEvents));

        return array_map(function (DomainEvent $event) use ($aggregate, $command, &$currentVersion) {
            $currentVersion = $currentVersion->increase();
            $genericEvent = $this->eventTranslator->translateFromDomainToGeneric(EventEnvelope::wrap(
                $event,
                $this->eventTranslator->nameOf($event),
                $aggregate->aggregateId(),
                $currentVersion,
                $this->aggregateTypeMap->aggregateType(),
                $command->metadata()
            ));

            $genericEvent = $this->addDefaultMetadata($genericEvent);

            $genericEvent = $genericEvent->withAddedMetadata(
                key: GenericEvent::META_CAUSATION_ID,
                value: $command->uuid()->toString()
            );

            return $genericEvent->withAddedMetadata(
                key: GenericEvent::META_CAUSATION_NAME,
                value: $command->commandName()->toString()
            );
        }, $domainEvents);
    }

    /**
     * @param AbstractAggregateRoot $aggregate Aggregate
     */
    private function deleteOrPersistAggregateState(AbstractAggregateRoot $aggregate): void
    {
        $aggregateState = $aggregate->state();

        if (
            $aggregateState instanceof DeletableState && $aggregateState->deleted()
        ) {
            $this->multiModelStore->deleteDoc(
                $this->stateCollectionName->toString(),
                (string) $aggregate->aggregateId()
            );
        } else {
            $doc = [
                'state' => $this->normalizeAggregateState($aggregateState),
                'version' => $aggregate->version()->toInt(),
            ];

            if ($metadata = $this->deriveAggregateMetadataFromState($aggregateState)) {
                $doc['metadata'] = $metadata;
            }

            $this->multiModelStore->upsertDoc(
                $this->stateCollectionName->toString(),
                $aggregate->aggregateId()->toString(),
                $doc
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeAggregateState(ImmutableState $aggregateState): array
    {
        return $this->documentStateTranslator->getToStoreState($aggregateState->toArray());
    }

    /**
     * @param class-string<ImmutableState> $stateClass
     * @param array<string, mixed>         $document
     */
    private function denormalizeAggregateState(string $stateClass, array $document): ImmutableState
    {
        return $stateClass::fromArray($this->documentStateTranslator->getToCreateState($document));
    }

    private function addDefaultMetadata(Message $genericEvent): Message
    {
        /**
         * @var MetadataEnricherInterface[] $metadataEnrichers
         */
        $metadataEnrichers = [
            new DefaultMetadataEnricher(),
        ];

        foreach ($metadataEnrichers as $enricher) {
            $genericEvent = $enricher->enrich($genericEvent);
        }

        return $genericEvent;
    }

    /**
     * @param GenericEvent[] $eeEvents
     */
    private function dispatchEvents(array $eeEvents): void
    {
        foreach ($eeEvents as $event) {
            event(new ProophEventStoredInStream($event, $this->getStreamName()));
        }
    }
}
