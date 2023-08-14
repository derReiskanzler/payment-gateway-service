<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Helpers;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\EventProjections\Services\Configurations\Environment;
use Allmyhomes\EventProjections\Services\Configurations\LaravelProjectionConfigurationProvider;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\Projection\ProjectionManager;

/**
 * @method streamName()
 */
trait RunProjectionTrait
{
    /**
     * @var array<string, array<string, Generator<int>>>
     */
    protected array $aggregateVersionGenerators = [];

    /**
     * @param string[] $projectionNames
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final protected function runProjections(array $projectionNames): void
    {
        foreach ($projectionNames as $projectionName) {
            $projectionsConfiguration = new LaravelProjectionConfigurationProvider('projections');
            $projectionConfiguration = $projectionsConfiguration->getProjectionConfiguration(
                $projectionName,
                LaravelProjectionConfigurationProvider::CONSUMING_PROJECTION_TYPE
            );

            /** @var ProjectionManager $projectionManager */
            $projectionManager = app()->get(ProjectionManager::class);
            if (Environment::SHARED === $projectionConfiguration->getEnvironment()) {
                $projectionManager = app()->make('SharedProjectionManager');
            }

            $eventHandler = app()->get($projectionConfiguration->getHandler());

            $projector = $projectionManager->createProjection($projectionName);
            $projector
                ->fromStreams(...$projectionConfiguration->getStreamNames())
                ->whenAny(
                    function (array $state, Message $event) use ($eventHandler): array {
                        $eventData = new EventDTO(
                            $event->uuid()->toString(),
                            $event->messageName(),
                            $event->payload(),
                            array_merge(
                                $event->metadata(),
                                [
                                    'stream_name' => $this->streamName(),
                                ]
                            ),
                            $event->createdAt()
                        );
                        $eventHandler->handle($eventData);

                        return $state;
                    }
                )
                ->run(false);
        }
    }

    protected function faker(): FakerGenerator
    {
        return Factory::create();
    }

    protected function nextAggregateVersion(string $aggregateType, string $aggregateId): int
    {
        $generator = $this->generateAggregateVersion($aggregateType, $aggregateId);
        $generator->next();

        return $generator->current();
    }

    /**
     * @return Generator<int>
     */
    private function generateAggregateVersion(string $aggregateType, string $aggregateId): Generator
    {
        if ($this->aggregateVersionGenerators[$aggregateType][$aggregateId] ?? null) {
            return $this->aggregateVersionGenerators[$aggregateType][$aggregateId];
        }

        return $this->aggregateVersionGenerators[$aggregateType][$aggregateId] = $this->createGenerator();
    }

    protected function currentAggregateVersion(string $aggregateType, string $aggregateId): int
    {
        $generator = $this->generateAggregateVersion($aggregateType, $aggregateId);

        return $generator->current();
    }

    /**
     * @return Generator<int>
     */
    private function createGenerator(): Generator
    {
        for ($i = 0; $i < PHP_INT_MAX; ++$i) {
            yield $i;
        }
    }
}
