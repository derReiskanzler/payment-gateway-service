<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs;

interface Command
{
    public function commandName(): CommandName;

    public function uuid(): CommandId;

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array;
}
