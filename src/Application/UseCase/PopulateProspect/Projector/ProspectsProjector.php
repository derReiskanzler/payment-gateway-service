<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateProspect\Projector;

use Allmyhomes\Application\UseCase\PopulateProspect\Document\Prospect;
use Allmyhomes\Application\UseCase\PopulateProspect\Repository\ProspectRepositoryInterface;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;

final class ProspectsProjector implements EventHandlerInterface
{
    private const USER_PROSPECT_PROFILE_CREATED = 'User.ProspectProfileCreated';
    private const USER_PROSPECT_PROFILE_UPDATED = 'User.ProspectProfileUpdated';
    private const USER_PROSPECT_PROFILE_DELETED = 'User.ProspectProfileDeleted';

    public function __construct(
        private ProspectRepositoryInterface $prospectRepository
    ) {
    }

    public function handle(EventDTO $event): void
    {
        switch ($event->getName()) {
            case self::USER_PROSPECT_PROFILE_CREATED:
            case self::USER_PROSPECT_PROFILE_UPDATED:
                $this->handleProspectProfileEvents($event);
                break;
            case self::USER_PROSPECT_PROFILE_DELETED:
                $this->handleProspectProfileDeletedEvent($event);
                break;
            default:
                break;
        }
    }

    public function handleProspectProfileEvents(EventDTO $event): void
    {
        $payload = $event->getPayload();

        $this->prospectRepository->upsert(
            new Prospect(
                ProspectId::fromString($payload['id']),
                ProspectEmail::fromString($payload['email']),
                $this->getProspectFirstName($payload),
                ProspectLastName::fromString($payload['last_name']),
                $this->getProspectSalutation($payload),
            )
        );
    }

    public function handleProspectProfileDeletedEvent(EventDTO $event): void
    {
        $this->prospectRepository->delete(ProspectId::fromString($event->getPayload()['id']));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function getProspectFirstName(array $payload): ?ProspectFirstName
    {
        return isset($payload['first_name']) ? ProspectFirstName::fromString($payload['first_name']) : null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function getProspectSalutation(array $payload): ?ProspectSalutation
    {
        return isset($payload['salutation']) ? ProspectSalutation::fromInt($payload['salutation']) : null;
    }
}
