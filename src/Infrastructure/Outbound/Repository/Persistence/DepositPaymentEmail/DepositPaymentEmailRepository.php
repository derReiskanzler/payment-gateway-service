<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Outbound\Repository\Persistence\DepositPaymentEmail;

use Allmyhomes\Domain\DepositPaymentEmail\Aggregate\DepositPaymentEmail;
use Allmyhomes\Domain\DepositPaymentEmail\Aggregate\DepositPaymentEmailState;
use Allmyhomes\Domain\DepositPaymentEmail\Event\DepositPaymentEmailSendingFailed;
use Allmyhomes\Domain\DepositPaymentEmail\Event\DepositPaymentEmailSentToProspect;
use Allmyhomes\Domain\DepositPaymentEmail\Repository\DepositPaymentEmailRepositoryInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventTranslator;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\MultiModelStoreAggregateRepository;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateTypeMap;

final class DepositPaymentEmailRepository extends MultiModelStoreAggregateRepository implements DepositPaymentEmailRepositoryInterface
{
    private const STREAM_NAME = 'payment_gateway-deposit_payment_email-stream';
    private const COLLECTION_NAME = 'deposit_payment_email';

    public function save(DepositPaymentEmail $depositPaymentEmail, Command $command): void
    {
        $this->saveAggregate($depositPaymentEmail, $command);
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
            AggregateTypeMap::AGGREGATE_BEHAVIOR_CLASS => DepositPaymentEmail::class,
            AggregateTypeMap::AGGREGATE_STATE_CLASS => DepositPaymentEmailState::class,
            AggregateTypeMap::AGGREGATE_TYPE => DepositPaymentEmail::TYPE,
        ]);
    }

    protected function getEventTranslatorMap(): EventTranslator
    {
        return new EventTranslator([
            DepositPaymentEmailSentToProspect::eventName() => DepositPaymentEmailSentToProspect::class,
            DepositPaymentEmailSendingFailed::eventName() => DepositPaymentEmailSendingFailed::class,
        ]);
    }
}
