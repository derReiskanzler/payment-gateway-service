<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventEngineProviders\Postgres;

use InvalidArgumentException;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\Container\AbstractEventStoreFactory;
use Prooph\EventStore\Pdo\PostgresEventStore;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;
use Prooph\EventStore\TransactionalEventStore;

class PostgresSharedEventStoreFactory extends AbstractEventStoreFactory
{
    /**
     * @return iterable<string, mixed>
     */
    public function defaultOptions(): iterable
    {
        return [
            'load_batch_size' => 1000,
            'event_streams_table' => 'event_streams',
            'message_factory' => FQCNMessageFactory::class,
            'wrap_action_event_emitter' => true,
            'metadata_enrichers' => [],
            'plugins' => [],
            'disable_transaction_handling' => false,
            'write_lock_strategy' => null,
        ];
    }

    /**
     * @param EventStore $eventStore EventStore
     */
    protected function createActionEventEmitterEventStore(EventStore $eventStore): ActionEventEmitterEventStore
    {
        if (!$eventStore instanceof TransactionalEventStore) {
            throw new InvalidArgumentException('EventStore has to be of type TransactionalEventStore');
        }

        return new TransactionalActionEventEmitterEventStore(
            $eventStore,
            new ProophActionEventEmitter([
                TransactionalActionEventEmitterEventStore::EVENT_APPEND_TO,
                TransactionalActionEventEmitterEventStore::EVENT_CREATE,
                TransactionalActionEventEmitterEventStore::EVENT_LOAD,
                TransactionalActionEventEmitterEventStore::EVENT_LOAD_REVERSE,
                TransactionalActionEventEmitterEventStore::EVENT_DELETE,
                TransactionalActionEventEmitterEventStore::EVENT_HAS_STREAM,
                TransactionalActionEventEmitterEventStore::EVENT_FETCH_STREAM_METADATA,
                TransactionalActionEventEmitterEventStore::EVENT_UPDATE_STREAM_METADATA,
                TransactionalActionEventEmitterEventStore::EVENT_FETCH_STREAM_NAMES,
                TransactionalActionEventEmitterEventStore::EVENT_FETCH_STREAM_NAMES_REGEX,
                TransactionalActionEventEmitterEventStore::EVENT_FETCH_CATEGORY_NAMES,
                TransactionalActionEventEmitterEventStore::EVENT_FETCH_CATEGORY_NAMES_REGEX,
                TransactionalActionEventEmitterEventStore::EVENT_BEGIN_TRANSACTION,
                TransactionalActionEventEmitterEventStore::EVENT_COMMIT,
                TransactionalActionEventEmitterEventStore::EVENT_ROLLBACK,
            ])
        );
    }

    /**
     * @return class-string<PostgresEventStore>
     */
    protected function eventStoreClassName(): string
    {
        return PostgresEventStore::class;
    }
}
