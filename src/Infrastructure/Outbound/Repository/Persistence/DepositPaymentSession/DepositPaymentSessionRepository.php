<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Outbound\Repository\Persistence\DepositPaymentSession;

use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSession;
use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSessionState;
use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCompleted;
use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCreated;
use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCreationFailed;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventTranslator;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\MultiModelStoreAggregateRepository;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateTypeMap;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\AggregateNotFound;

final class DepositPaymentSessionRepository extends MultiModelStoreAggregateRepository implements DepositPaymentSessionRepositoryInterface
{
    private const STREAM_NAME = 'payment_gateway-deposit_payment_session-stream';
    private const COLLECTION_NAME = 'deposit_payment_session';

    public function save(DepositPaymentSession $depositPaymentSession, Command $command): void
    {
        $this->saveAggregate($depositPaymentSession, $command);
    }

    public function getById(ReservationId $id): DepositPaymentSession|null
    {
        try {
            $depositPaymentSession = $this->getAggregate(AggregateId::fromString($id->toString()));
        } catch (AggregateNotFound $e) {
            return null;
        }

        if (!$depositPaymentSession instanceof DepositPaymentSession) {
            return null;
        }

        return $depositPaymentSession;
    }

    protected function getStreamName(): string
    {
        return self::STREAM_NAME;
    }

    protected function getCollectionName(): string
    {
        return self::COLLECTION_NAME;
    }

    protected function getAggregateTypeMap(): AggregateTypeMap
    {
        return AggregateTypeMap::fromArray([
            AggregateTypeMap::AGGREGATE_BEHAVIOR_CLASS => DepositPaymentSession::class,
            AggregateTypeMap::AGGREGATE_STATE_CLASS => DepositPaymentSessionState::class,
            AggregateTypeMap::AGGREGATE_TYPE => DepositPaymentSession::TYPE,
        ]);
    }

    protected function getEventTranslatorMap(): EventTranslator
    {
        return new EventTranslator([
            DepositPaymentSessionCreated::eventName() => DepositPaymentSessionCreated::class,
            DepositPaymentSessionCreationFailed::eventName() => DepositPaymentSessionCreationFailed::class,
            DepositPaymentSessionCompleted::eventName() => DepositPaymentSessionCompleted::class,
        ]);
    }
}
