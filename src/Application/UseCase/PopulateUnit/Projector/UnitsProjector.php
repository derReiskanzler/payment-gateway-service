<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateUnit\Projector;

use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Domain\ValueObject\UnitId;
use Allmyhomes\Domain\ValueObject\UnitName;
use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Exception;

final class UnitsProjector implements EventHandlerInterface
{
    private const PROJECT_INFORMATION_PLATFORM_UNIT_CONTENT_PUBLISHED = 'ProjectInformation.PlatformUnitContentPublished';
    private const PROJECT_INFORMATION_PLATFORM_UNIT_CONTENT_UPDATED = 'ProjectInformation.PlatformUnitContentUpdated';

    public function __construct(
        private UnitRepositoryInterface $unitRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(EventDTO $event): void
    {
        switch ($event->getName()) {
            case self::PROJECT_INFORMATION_PLATFORM_UNIT_CONTENT_PUBLISHED:
            case self::PROJECT_INFORMATION_PLATFORM_UNIT_CONTENT_UPDATED:
                $this->handlePlatformUnitContentEvents($event);
                break;
            default:
                break;
        }
    }

    private function handlePlatformUnitContentEvents(EventDTO $event): void
    {
        $payload = $event->getPayload();

        if ($this->isNonGermanContent($payload)) {
            return;
        }

        $unit = new Unit(
            UnitId::fromInt($payload['unit_id']),
            $this->getUnitName($payload),
        );

        $this->unitRepository->upsert($unit);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function isNonGermanContent(array $payload): bool
    {
        return 'de-DE' !== $payload['language_code'];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function getUnitName(array $payload): ?UnitName
    {
        return isset($payload['name']) ? UnitName::fromString($payload['name']) : null;
    }
}
