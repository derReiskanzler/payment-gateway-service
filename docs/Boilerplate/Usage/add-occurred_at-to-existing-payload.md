# Migrating existing event streams with `occurred_at`

This guide of how to migrate existing event streams with `occurred_at` field.

## When to Migrate

With `allmyhomes/laravel-ddd-abstractions` v2.3.0 or with `boilerplate` v3.1.x, there will be the ability to include `occurred_at`
inside the event payload by specifying

- `$includeOccurredAt` to `true` -> default value if service use DDD Abstraction Package
- use `ImmutableEventTrait` inside events class if service uses ES/CQRS

To have a consistent event payload for old events, would need to have the needed migrations as mentioned in [Process](#process).

## Process

- Create a migration for the shared event stream on our own database, you can extend different `StreamMigration` for `MySQL` (MySqlOccurredAtStreamMigration) and `Postgres` (PostgresOccurredAtStreamMigration)

```php
<?php

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations\PostgresOccurredAtStreamMigration;

class AddOccurredAtToLeadEventStream extends PostgresOccurredAtStreamMigration
{
    protected $streamName = 'boilerplate-leads-stream';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        parent::addOccurredAt();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        parent::rollbackOccurredAt();
    }
}
```

- Create a migration for the shared event stream on the EventStore

```php
<?php

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations\EventStorePostgresOccurredAtStreamMigration;

class AddOccurredAtToLeadEventStream extends EventStorePostgresOccurredAtStreamMigration
{
    protected $streamName = 'boilerplate-leads-stream';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        parent::addOccurredAt();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        parent::rollbackOccurredAt();
    }
}
```

- run the migration by `art migrate`
