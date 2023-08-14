<?php

declare(strict_types=1);

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations\EventStreamMigration;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;

final class CreateUserUsersStream extends EventStreamMigration
{
    private const STREAM_NAME = 'user-users-stream';

    private EventStore $sharedStore;

    public function __construct()
    {
        parent::__construct();
        $this->sharedStore = app()->make('SharedEventStore');
    }

    public function up(): void
    {
        if (app()->environment('testing')) {
            $this->migrateOnEventStore(self::STREAM_NAME);
        }
    }

    public function down(): void
    {
        if (app()->environment('testing')) {
            $this->sharedStore->delete(new StreamName(self::STREAM_NAME));
        }
    }
}
