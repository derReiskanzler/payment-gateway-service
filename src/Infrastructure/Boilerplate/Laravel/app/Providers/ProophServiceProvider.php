<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer\DocumentStateTranslatorInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer\SnakeCaseDocumentStateTranslator;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventEngineProviders\Postgres\PostgresSharedEventStoreFactory;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventEngineProviders\Postgres\PostgresSingleStreamStrategy;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventEngineProviders\TransactionalConnection;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer\EventPayloadTranslatorInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer\SnakeCaseEventPayloadTranslator;
use EventEngine\DocumentStore\DocumentStore;
use EventEngine\DocumentStore\Postgres\PostgresDocumentStore;
use EventEngine\EventStore\EventStore;
use EventEngine\Persistence\ComposedMultiModelStore;
use EventEngine\Persistence\MultiModelStore;
use EventEngine\Persistence\TransactionalConnection as EventEngineTransactionalConnection;
use EventEngine\Prooph\V7\EventStore\ProophEventStore;
use EventEngine\Prooph\V7\EventStore\ProophEventStoreMessageFactory;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use PDO;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\EventStore as ProophV7EventStore;
use Prooph\EventStore\Pdo\Container\PostgresEventStoreFactory;
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\Pdo\PersistenceStrategy\PostgresPersistenceStrategy;
use Prooph\EventStore\Pdo\PostgresEventStore;
use Prooph\EventStore\Pdo\Projection\PostgresProjectionManager;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;

/**
 * Class ProophServiceProvider.
 */
class ProophServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $path = $this->getConfigPath();
        $this->publishes(
            [
                $path.'/prooph.php' => config_path('prooph.php'),
            ],
            'config'
        );
    }

    public function register(): void
    {
        $path = $this->getConfigPath();

        $this->mergeConfigFrom($path.'/prooph.php', 'prooph');

        $this->bind();
    }

    /**
     * @throws BindingResolutionException
     */
    protected function proophEventStorePersistenceStrategy(): PostgresPersistenceStrategy
    {
        $this->app->singleton(PersistenceStrategy::class, static fn () => new PostgresSingleStreamStrategy());

        $this->app->singleton(PostgresPersistenceStrategy::class, static fn () => new PostgresSingleStreamStrategy());

        return $this->app->make(PostgresPersistenceStrategy::class);
    }

    private function getConfigPath(): string
    {
        return \dirname(__DIR__).'/../config';
    }

    private function bind(): void
    {
        /*
         * Bind Pdo Connection Names
         */
        $this->bindPdoConnectionName();

        /*
         * Bind Internal Store
         */
        $this->bindEventStore();
        $this->bindDocumentStore();
        $this->bindInternalMultiModelStore();
        $this->bindInternalProjectionManager();

        /*
         * Bind External Store
         */
        $this->bindSharedEventStore();

        /*
         * Bind EventPayload Translator for snake and camel cases
         */
        $this->bindEventPayloadTranslator();

        /*
         * Bind DocumentState Translator for snake and camel cases
         */
        $this->bindDocumentStateTranslator();
    }

    private function bindPdoConnectionName(): void
    {
        $this->app->singleton($this->pdoConnectionName(), static fn () => DB::connection()->getPdo());

        $this->app->singleton($this->sharedPdoConnectionName(), fn () => DB::connection($this->getEventStoreConnection())->getPdo());
    }

    private function bindEventStore(): void
    {
        $this->app->singleton(PostgresEventStoreFactory::class, static fn () => new PostgresEventStoreFactory());

        $this->app->singleton(ProophV7EventStore::class, static fn ($app) => $app->make(PostgresEventStoreFactory::class)->__invoke($app->make(Container::class), ProophV7EventStore::class));
    }

    private function bindDocumentStore(): void
    {
        $this->app->singleton(
            DocumentStore::class,
            function () {
                return new PostgresDocumentStore(
                    $this->pdoConnection(),
                    '', //No table prefix
                    'CHAR(36) NOT NULL', //Use alternative docId schema, to allow uuids as well as md5 hashes
                    false //Disable transaction handling, as this is controlled by the MultiModelStore
                );
            }
        );
    }

    private function bindInternalMultiModelStore(): void
    {
        $this->app->singleton(MultiModelStore::class, function () {
            return new ComposedMultiModelStore(
                $this->transactionalConnection(),
                $this->eventEngineEventStore(),
                $this->documentStore()
            );
        });
    }

    private function bindInternalProjectionManager(): void
    {
        $this->app->singleton(ProjectionManager::class, function () {
            return new PostgresProjectionManager(
                $this->proophPostgresEventStore(),
                $this->pdoConnection()
            );
        });
    }

    private function bindSharedEventStore(): void
    {
        $this->app->singleton(PostgresSharedEventStoreFactory::class, static fn () => new PostgresSharedEventStoreFactory('shared'));

        $this->app->singleton('SharedEventStore', static fn ($app) => $app->make(PostgresSharedEventStoreFactory::class)->__invoke($app->make(Container::class), 'SharedEventStore'));
    }

    /**
     * @throws BindingResolutionException
     */
    private function transactionalConnection(): EventEngineTransactionalConnection
    {
        $this->app->singleton(EventEngineTransactionalConnection::class, fn () => new TransactionalConnection($this->pdoConnection()));

        return $this->app->make(EventEngineTransactionalConnection::class);
    }

    /**
     * @throws BindingResolutionException
     */
    private function eventEngineEventStore(): EventStore
    {
        $this->app->singleton(EventStore::class, fn () => new ProophEventStore($this->proophPostgresEventStore()));

        return $this->app->make(EventStore::class);
    }

    /**
     * @throws BindingResolutionException
     */
    private function proophPostgresEventStore(): ProophV7EventStore
    {
        $this->app->singleton(ProophV7EventStore::class, function () {
            $eventStore = new PostgresEventStore(
                new ProophEventStoreMessageFactory(),
                $this->pdoConnection(),
                $this->proophEventStorePersistenceStrategy()
            );

            return new TransactionalActionEventEmitterEventStore(
                $eventStore,
                new ProophActionEventEmitter(TransactionalActionEventEmitterEventStore::ALL_EVENTS)
            );
        });

        return $this->app->make(ProophV7EventStore::class);
    }

    /**
     * @throws BindingResolutionException
     */
    private function documentStore(): DocumentStore
    {
        $this->app->singleton(DocumentStore::class, function () {
            return new PostgresDocumentStore(
                $this->pdoConnection(),
                '', //No table prefix
                'CHAR(36) NOT NULL', //Use alternative docId schema, to allow uuids as well as md5 hashes
                false //Disable transaction handling, as this is controlled by the MultiModelStore
            );
        });

        return $this->app->make(DocumentStore::class);
    }

    /**
     * @throws BindingResolutionException
     */
    private function pdoConnection(): PDO
    {
        $this->app->singleton(PDO::class, static fn () => DB::connection()->getPdo());

        return $this->app->make(PDO::class);
    }

    /**
     * @return Repository|mixed
     */
    private function pdoConnectionName(): mixed
    {
        return config('database.laravel_pdo_connection_name');
    }

    /**
     * @return Repository|mixed
     */
    private function sharedPdoConnectionName(): mixed
    {
        return config('database.pg_event_store_pdo_connection_name');
    }

    /**
     * @return Repository|mixed
     */
    private function getEventStoreConnection(): mixed
    {
        return config('database.event_store_connection');
    }

    private function bindEventPayloadTranslator(): void
    {
        $this->app->bind(
            EventPayloadTranslatorInterface::class,
            SnakeCaseEventPayloadTranslator::class
        );
    }

    private function bindDocumentStateTranslator(): void
    {
        $this->app->bind(
            DocumentStateTranslatorInterface::class,
            SnakeCaseDocumentStateTranslator::class
        );
    }
}
