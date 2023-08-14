<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event;

final class Metadata
{
    public const AGGREGATE_ID = 'aggregate_id';
    public const AGGREGATE_VERSION = 'aggregate_version';
    public const AGGREGATE_TYPE = 'aggregate_type';
    public const CAUSATION_ID = 'causation_id';
    public const CAUSATION_NAME = 'causation_name';
}
