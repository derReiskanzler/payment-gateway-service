<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventEngineProviders;

use EventEngine\Persistence\TransactionalConnection as EventEngineTransactionalConnection;
use PDO;

class TransactionalConnection implements EventEngineTransactionalConnection
{
    public function __construct(private PDO $connection)
    {
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollBack(): void
    {
        $this->connection->rollBack();
    }
}
