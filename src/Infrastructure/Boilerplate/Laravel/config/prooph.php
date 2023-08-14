<?php

declare(strict_types=1);

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventEngineProviders\Postgres\PostgresSingleStreamStrategy;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Prooph\Messaging\GenericMessageFactory;
use Prooph\EventStore\Projection\Projector;

return [
    'projection_manager' => [
        'default' => [
            'connection' => config('database.laravel_pdo_connection_name'),
            'persistence_strategy' => PostgresSingleStreamStrategy::class,
            'load_batch_size' => 1000,
            'event_streams_table' => 'event_streams',
            'message_factory' => GenericMessageFactory::class,
            'projector_options' => [
                Projector::OPTION_PERSIST_BLOCK_SIZE => 1, // this is mandatory, don't change it
                Projector::OPTION_CACHE_SIZE => Projector::DEFAULT_CACHE_SIZE,
                Projector::OPTION_LOCK_TIMEOUT_MS => Projector::DEFAULT_LOCK_TIMEOUT_MS * 30,
                Projector::OPTION_PCNTL_DISPATCH => Projector::DEFAULT_PCNTL_DISPATCH,
                Projector::OPTION_SLEEP => Projector::DEFAULT_SLEEP * 3, // unit is microsecond
                Projector::OPTION_UPDATE_LOCK_THRESHOLD => Projector::DEFAULT_UPDATE_LOCK_THRESHOLD,
            ],
        ],
        'shared' => [
            'connection' => config('database.pg_event_store_pdo_connection_name'),
            'persistence_strategy' => PostgresSingleStreamStrategy::class,
            'load_batch_size' => 1000,
            'event_streams_table' => 'event_streams',
            'message_factory' => GenericMessageFactory::class,
            'event_store' => 'SharedEventStore',
            'projector_options' => [
                Projector::OPTION_PERSIST_BLOCK_SIZE => 1, // this is mandatory, don't change it
                Projector::OPTION_CACHE_SIZE => Projector::DEFAULT_CACHE_SIZE,
                Projector::OPTION_LOCK_TIMEOUT_MS => Projector::DEFAULT_LOCK_TIMEOUT_MS * 30,
                Projector::OPTION_PCNTL_DISPATCH => Projector::DEFAULT_PCNTL_DISPATCH,
                Projector::OPTION_SLEEP => Projector::DEFAULT_SLEEP * 3, // unit is microsecond
                Projector::OPTION_UPDATE_LOCK_THRESHOLD => Projector::DEFAULT_UPDATE_LOCK_THRESHOLD,
            ],
        ],
    ],
    'event_store' => [
        'default' => [
            'wrap_action_event_emitter' => true,
            'metadata_enrichers' => [],
            'plugins' => [],
            'connection' => config('database.laravel_pdo_connection_name'),
            'persistence_strategy' => PostgresSingleStreamStrategy::class,
            'load_batch_size' => 1000,
            'event_streams_table' => 'event_streams',
            'message_factory' => GenericMessageFactory::class,
        ],
        'shared' => [
            'wrap_action_event_emitter' => true,
            'metadata_enrichers' => [],
            'plugins' => [],
            'connection' => config('database.pg_event_store_pdo_connection_name'),
            'persistence_strategy' => PostgresSingleStreamStrategy::class,
            'load_batch_size' => 1000,
            'event_streams_table' => 'event_streams',
            'message_factory' => GenericMessageFactory::class,
        ],
    ],
];
