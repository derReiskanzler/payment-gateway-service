<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations;

interface OccurredAtStreamMigrationInterface
{
    public function addOccurredAt(): void;

    public function rollbackOccurredAt(): void;
}
