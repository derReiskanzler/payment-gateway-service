<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\PopulateUnit\Projector;

use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit;
use Allmyhomes\Application\UseCase\PopulateUnit\Projector\UnitsProjector;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Domain\ValueObject\UnitId;
use Allmyhomes\Domain\ValueObject\UnitName;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UnitsProjectorTest extends TestCase
{
    /**
     * @var MockObject&UnitRepositoryInterface
     */
    private MockObject $repository;
    private UnitsProjector $projector;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UnitRepositoryInterface::class);
        $this->projector = new UnitsProjector($this->repository);
    }

    /**
     * @throws \Exception
     *
     * @dataProvider provideUnitContentEvents
     */
    public function testHandlePlatformUnitContentEvents(EventDTO $event, Unit $unit): void
    {
        $this->repository
            ->expects($this->once())
            ->method('upsert')
            ->with($unit);

        $this->projector->handle($event);
    }

    /**
     * @dataProvider provideSkipableEvents
     */
    public function testNonGermanContents(EventDTO $event): void
    {
        $this->repository
            ->expects(self::never())
            ->method('upsert');

        $this->projector->handle($event);
    }

    /**
     * @return Generator<mixed>
     */
    public function provideUnitContentEvents(): Generator
    {
        $unitId = 42;
        $unitName = 'WE 01';
        yield 'PlatformUnitContentPublished with full payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ProjectInformation.PlatformUnitContentPublished',
                [
                    'id' => '19a7bff8-8a41-47af-bc86-c5cf131e6a04',
                    'unit_id' => $unitId,
                    'project_id' => 80262,
                    'name' => $unitName,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                    'language_code' => 'de-DE',
                    'price' => [
                        'value' => 65.88,
                        'currency' => 'EUR',
                    ],
                    'size' => [
                        'value' => 37.5,
                        'measure' => 'm²',
                    ],
                    'rooms' => 2.5,
                    'floor' => '1.0 EG',
                ],
                []
            ),
            new Unit(
                UnitId::fromInt($unitId),
                UnitName::fromString($unitName),
            ),
        ];

        yield 'PlatformUnitContentPublished without optionals' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ProjectInformation.PlatformUnitContentPublished',
                [
                    'id' => '19a7bff8-8a41-47af-bc86-c5cf131e6a04',
                    'unit_id' => $unitId,
                    'project_id' => 80262,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                    'language_code' => 'de-DE',
                ],
                []
            ),
            new Unit(
                UnitId::fromInt($unitId),
                null,
            ),
        ];

        yield 'PlatformUnitContentUpdated' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ProjectInformation.PlatformUnitContentUpdated',
                [
                    'id' => '19a7bff8-8a41-47af-bc86-c5cf131e6a04',
                    'unit_id' => $unitId,
                    'project_id' => 80262,
                    'name' => $unitName,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                    'language_code' => 'de-DE',
                ],
                []
            ),
            new Unit(
                UnitId::fromInt($unitId),
                UnitName::fromString($unitName),
            ),
        ];
    }

    /**
     * @return Generator<mixed>
     */
    public function provideSkipableEvents(): Generator
    {
        yield 'PlatformUnitContentPublished' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ProjectInformation.PlatformUnitContentPublished',
                [
                    'id' => '19a7bff8-8a41-47af-bc86-c5cf131e6a04',
                    'unit_id' => 42,
                    'project_id' => 3,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                    'language_code' => 'en-US',
                ],
                []
            ),
        ];

        yield 'PlatformUnitContentUpdated' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ProjectInformation.PlatformUnitContentUpdated',
                [
                    'id' => '19a7bff8-8a41-47af-bc86-c5cf131e6a04',
                    'unit_id' => 42,
                    'project_id' => 3,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                    'language_code' => 'en-US',
                ],
                []
            ),
        ];

        yield 'OtherEvent' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'Other.Event',
                [
                    'id' => '19a7bff8-8a41-47af-bc86-c5cf131e6a04',
                    'unit_id' => 42,
                    'project_id' => 3,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                    'language_code' => 'en-US',
                ],
                []
            ),
        ];
    }
}
