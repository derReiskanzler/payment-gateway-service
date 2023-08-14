<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\SendDepositPaymentEmailToProspect\ProcessManager;

use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspect;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspectHandlerInterface;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\ProcessManager\DepositPaymentSessionCreatedProcessManager;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DepositPaymentSessionCreatedProcessManagerTest extends TestCase
{
    private DepositPaymentSessionCreatedProcessManager $processManager;
    /**
     * @var CommandIdGeneratorInterface&MockObject
     */
    private CommandIdGeneratorInterface $commandIdGenerator;

    /**
     * @var SendDepositPaymentEmailToProspecthandlerInterface&MockObject
     */
    private SendDepositPaymentEmailToProspecthandlerInterface $sendDepositPaymentEmailToProspecthandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandIdGenerator = $this->createMock(CommandIdGeneratorInterface::class);
        $this->sendDepositPaymentEmailToProspecthandler = $this->createMock(SendDepositPaymentEmailToProspecthandlerInterface::class);

        $this->processManager = new DepositPaymentSessionCreatedProcessManager(
            $this->commandIdGenerator,
            $this->sendDepositPaymentEmailToProspecthandler,
        );
    }

    /**
     * @dataProvider provideDepositPaymentSessionCreatedEvent
     */
    public function testHandle(EventDTO $event, SendDepositPaymentEmailToProspect $command): void
    {
        $this->sendDepositPaymentEmailToProspecthandler
            ->expects(self::once())
            ->method('handle')
            ->with($command);

        $this->commandIdGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn($command->uuid());

        $this->processManager->handle($event);
    }

    /**
     * @dataProvider provideOtherEvent
     */
    public function testHandleOtherEvent(EventDTO $event): void
    {
        $this->sendDepositPaymentEmailToProspecthandler
            ->expects($this->never())
            ->method('handle');

        $this->commandIdGenerator
            ->expects($this->never())
            ->method('generate');

        $this->processManager->handle($event);
    }

    public function provideDepositPaymentSessionCreatedEvent(): Generator
    {
        $reservationId = '1111-2222-3333';
        $unitIds = [1, 2, 3];
        $checkoutSessionId = 'cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2';
        $checkoutSessionUrl = 'https://www.example.com';
        $expiresAt = '2016-06-16T16:00:00+00:00';

        yield 'DepositPaymentSessionCreated Event with full payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'PaymentGateway.DepositPaymentSessionCreated',
                [
                    'reservation_id' => $reservationId,
                    'checkout_session_id' => $checkoutSessionId,
                    'agent_id' => 'da7c58f5-4c74-4722-8b94-7fcf8d857055',
                    'project_id' => 80262,
                    'prospect_id' => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                    'unit_ids' => $unitIds,
                    'total_unit_deposit' => 6000.00,
                    'language' => 'de',
                    'currency' => 'eur',
                    'customer_id' => 'customer id',
                    'payment_intent_id' => 'payment intent id',
                    'payment_status' => 'unpaid',
                    'checkout_session_status' => 'open',
                    'checkout_session_url' => $checkoutSessionUrl,
                    'expires_at' => $expiresAt,
                    'created_at' => '2016-06-16T16:00:00+00:00',
                ],
                []
            ),
            new SendDepositPaymentEmailToProspect(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                ReservationId::fromString($reservationId),
                CheckoutSessionId::fromString($checkoutSessionId),
                CheckoutSessionUrl::fromString($checkoutSessionUrl),
                ExpiresAt::fromString($expiresAt),
            ),
        ];
    }

    /**
     * @return Generator<mixed>
     */
    public function provideOtherEvent(): Generator
    {
        yield 'Other Event with empty payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'Other.Event',
                [],
                []
            ),
        ];
    }
}
