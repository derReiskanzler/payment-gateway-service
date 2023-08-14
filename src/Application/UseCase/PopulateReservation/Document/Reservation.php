<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateReservation\Document;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Domain\ValueObject\TotalUnitDeposit;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\ReadModel;

final class Reservation implements ReadModel
{
    private const ID = 'id';
    private const AGENT_ID = 'agent_id';
    private const DEPOSIT_TRANSFER_DEADLINE = 'deposit_transfer_deadline';
    private const LANGUAGE = 'language';
    private const PROJECT_ID = 'project_id';
    private const PROSPECT_ID = 'prospect_id';
    private const TOTAL_UNIT_DEPOSIT = 'total_unit_deposit';
    private const UNITS = 'units';

    public function __construct(
        private ReservationId $id,
        private AgentId $agentId,
        private ?DepositTransferDeadline $depositTransferDeadlineDate,
        private Language $language,
        private ProjectId $projectId,
        private ProspectId $prospectId,
        private TotalUnitDeposit $totalUnitDeposit,
        private UnitCollection $units,
    ) {
    }

    public function id(): ReservationId
    {
        return $this->id;
    }

    public function agentId(): AgentId
    {
        return $this->agentId;
    }

    public function depositTransferDeadline(): ?DepositTransferDeadline
    {
        return $this->depositTransferDeadlineDate;
    }

    public function language(): Language
    {
        return $this->language;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function prospectId(): ProspectId
    {
        return $this->prospectId;
    }

    public function totalUnitDeposit(): TotalUnitDeposit
    {
        return $this->totalUnitDeposit;
    }

    public function units(): UnitCollection
    {
        return $this->units;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::ID => $this->id()->toString(),
            self::AGENT_ID => $this->agentId()->toString(),
            self::DEPOSIT_TRANSFER_DEADLINE => $this->depositTransferDeadline()?->toString(),
            self::LANGUAGE => $this->language()->toString(),
            self::PROJECT_ID => $this->projectId()->toInt(),
            self::PROSPECT_ID => $this->prospectId()->toString(),
            self::TOTAL_UNIT_DEPOSIT => $this->totalUnitDeposit()->toFloat(),
            self::UNITS => $this->units()->toArray(),
        ];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ReservationId::fromString($data[self::ID]),
            AgentId::fromString($data[self::AGENT_ID]),
            isset($data[self::DEPOSIT_TRANSFER_DEADLINE]) ? DepositTransferDeadline::fromString($data[self::DEPOSIT_TRANSFER_DEADLINE]) : null,
            Language::fromString($data[self::LANGUAGE]),
            ProjectId::fromInt($data[self::PROJECT_ID]),
            ProspectId::fromString($data[self::PROSPECT_ID]),
            TotalUnitDeposit::fromFloat($data[self::TOTAL_UNIT_DEPOSIT]),
            UnitCollection::fromArray($data[self::UNITS]),
        );
    }
}
