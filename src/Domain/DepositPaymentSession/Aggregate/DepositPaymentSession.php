<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Aggregate;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCompleted;
use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCreated;
use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCreationFailed;
use Allmyhomes\Domain\DepositPaymentSession\Exception\CouldNotCreateCheckoutSessionException;
use Allmyhomes\Domain\DepositPaymentSession\Exception\DepositDisabledException;
use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\AbstractAggregateRoot;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;

final class DepositPaymentSession extends AbstractAggregateRoot
{
    /** @var DepositPaymentSessionState&ImmutableState */
    protected DepositPaymentSessionState|ImmutableState $state;

    public const TYPE = Context::NAME.'DepositPaymentSession';

    public static function createNewDepositPaymentSession(
        ReservationId $reservationId,
        AgentId $agentId,
        ProjectId $projectId,
        ProspectId $prospectId,
        Language $language,
        ?DepositTransferDeadline $depositTransferDeadline,
        UnitCollection $unitCollection,
        StripeServiceInterface $stripeService,
    ): self {
        if ($unitCollection->totalUnitDeposit()->isEmpty()) {
            throw DepositDisabledException::forReservationId($reservationId, $projectId);
        }

        $self = new self();

        $self->createDepositPaymentSession(
            $reservationId,
            $agentId,
            $projectId,
            $prospectId,
            $language,
            $depositTransferDeadline,
            $unitCollection,
            $stripeService,
            ErrorCount::fromInt(0),
        );

        return $self;
    }

    public function retryDepositPaymentSessionCreation(
        ReservationId $reservationId,
        AgentId $agentId,
        ProjectId $projectId,
        ProspectId $prospectId,
        Language $language,
        ?DepositTransferDeadline $depositTransferDeadline,
        UnitCollection $unitCollection,
        StripeServiceInterface $stripeService,
        ErrorCount $errorCount,
    ): void {
        if ($errorCount->exceedsMaxErrorCount()) {
            throw CouldNotCreateCheckoutSessionException::forReservationId($reservationId, $errorCount);
        }

        $this->createDepositPaymentSession(
            $reservationId,
            $agentId,
            $projectId,
            $prospectId,
            $language,
            $depositTransferDeadline,
            $unitCollection,
            $stripeService,
            $errorCount,
        );
    }

    private function createDepositPaymentSession(
        ReservationId $reservationId,
        AgentId $agentId,
        ProjectId $projectId,
        ProspectId $prospectId,
        Language $language,
        ?DepositTransferDeadline $depositTransferDeadline,
        UnitCollection $unitCollection,
        StripeServiceInterface $stripeService,
        ErrorCount $errorCount,
    ): void {
        $checkoutSession = $stripeService->createCheckoutSession(
            $reservationId,
            $agentId,
            $projectId,
            $prospectId,
            $language,
            $unitCollection,
            $depositTransferDeadline,
        );

        if (empty($checkoutSession)) {
            $this->recordThat(DepositPaymentSessionCreationFailed::fromRecordData([
                DepositPaymentSessionCreationFailed::RESERVATION_ID => $reservationId,
                DepositPaymentSessionCreationFailed::ERROR_COUNT => $errorCount->increase(),
                DepositPaymentSessionCreationFailed::CREATED_AT => CreatedAt::fromDateTime(new \DateTimeImmutable()),
            ]));
        } else {
            $this->recordThat(DepositPaymentSessionCreated::fromRecordData([
                DepositPaymentSessionCreated::RESERVATION_ID => $reservationId,
                DepositPaymentSessionCreated::AGENT_ID => $agentId,
                DepositPaymentSessionCreated::LANGUAGE => $language,
                DepositPaymentSessionCreated::PROJECT_ID => $projectId,
                DepositPaymentSessionCreated::PROSPECT_ID => $prospectId,
                DepositPaymentSessionCreated::TOTAL_UNIT_DEPOSIT => $unitCollection->totalUnitDeposit(),
                DepositPaymentSessionCreated::UNIT_IDS => $unitCollection->idCollection(),

                DepositPaymentSessionCreated::CHECKOUT_SESSION_ID => $checkoutSession->id(),
                DepositPaymentSessionCreated::CHECKOUT_SESSION_STATUS => $checkoutSession->status(),
                DepositPaymentSessionCreated::CHECKOUT_SESSION_URL => $checkoutSession->url(),
                DepositPaymentSessionCreated::CREATED_AT => CreatedAt::fromDateTime(new \DateTimeImmutable()),
                DepositPaymentSessionCreated::CURRENCY => $checkoutSession->currency(),
                DepositPaymentSessionCreated::CUSTOMER_ID => $checkoutSession->customerId(),
                DepositPaymentSessionCreated::EXPIRES_AT => $checkoutSession->expiresAt(),
                DepositPaymentSessionCreated::PAYMENT_INTENT_ID => $checkoutSession->paymentIntentId(),
                DepositPaymentSessionCreated::PAYMENT_STATUS => $checkoutSession->paymentStatus(),
            ]));
        }
    }

    public function completeDepositPaymentSession(
        CheckoutSessionId $checkoutSessionId,
        CheckoutSessionStatus $status,
        PaymentStatus $paymentStatus,
    ): void {
        if ($status->matches(CheckoutSessionStatus::COMPLETE)) {
            $this->recordThat(DepositPaymentSessionCompleted::fromRecordData([
                DepositPaymentSessionCompleted::RESERVATION_ID => $this->state->reservationId(),
                DepositPaymentSessionCompleted::CHECKOUT_SESSION_ID => $checkoutSessionId,
                DepositPaymentSessionCompleted::STATUS => $status,
                DepositPaymentSessionCompleted::PAYMENT_STATUS => $paymentStatus,
                DepositPaymentSessionCompleted::CREATED_AT => CreatedAt::fromDateTime(new \DateTimeImmutable()),
            ]));
        }
    }

    public function whenDepositPaymentSessionCreated(
        DepositPaymentSessionCreated $event
    ): DepositPaymentSessionState {
        return DepositPaymentSessionState::fromRecordData([
            DepositPaymentSessionState::RESERVATION_ID => $event->reservationId(),
            DepositPaymentSessionState::CHECKOUT_SESSION_ID => $event->checkoutSessionId(),
            DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $event->checkoutSessionStatus(),
            DepositPaymentSessionState::PAYMENT_STATUS => $event->paymentStatus(),
            DepositPaymentSessionState::ERROR_COUNT => ErrorCount::fromInt(0),
        ]);
    }

    public function whenDepositPaymentSessionCreationFailed(
        DepositPaymentSessionCreationFailed $event
    ): DepositPaymentSessionState {
        return DepositPaymentSessionState::fromRecordData([
            DepositPaymentSessionState::RESERVATION_ID => $event->reservationId(),
            DepositPaymentSessionState::ERROR_COUNT => $event->errorCount(),
        ]);
    }

    public function whenDepositPaymentSessionCompleted(
        DepositPaymentSessionCompleted $event
    ): DepositPaymentSessionState {
        return $this->state->with([
            DepositPaymentSessionState::CHECKOUT_SESSION_ID => $event->checkoutSessionId(),
            DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $event->status(),
            DepositPaymentSessionState::PAYMENT_STATUS => $event->paymentStatus(),
        ]);
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromValueObject($this->state->reservationId());
    }
}
