<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;

interface CommandIdGeneratorInterface
{
    public function generate(): CommandId;
}
