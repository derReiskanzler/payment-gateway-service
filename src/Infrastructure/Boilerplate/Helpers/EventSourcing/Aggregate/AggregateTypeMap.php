<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate;

use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class AggregateTypeMap implements ImmutableRecord
{
    use ImmutableRecordLogic;

    public const AGGREGATE_TYPE = 'aggregateType';
    public const AGGREGATE_BEHAVIOR_CLASS = 'aggregateBehaviorClass';
    public const AGGREGATE_STATE_CLASS = 'aggregateStateClass';

    private AggregateStateClass $aggregateStateClass;

    private AggregateBehaviorClass $aggregateBehaviorClass;

    private AggregateType $aggregateType;

    public function aggregateStateClass(): AggregateStateClass
    {
        return $this->aggregateStateClass;
    }

    public function aggregateBehaviorClass(): AggregateBehaviorClass
    {
        return $this->aggregateBehaviorClass;
    }

    public function aggregateType(): AggregateType
    {
        return $this->aggregateType;
    }
}
