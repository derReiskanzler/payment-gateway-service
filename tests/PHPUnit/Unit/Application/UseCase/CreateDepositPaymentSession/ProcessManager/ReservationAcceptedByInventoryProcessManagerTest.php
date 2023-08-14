<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\CreateDepositPaymentSession\ProcessManager;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSession;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSessionHandlerInterface;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\ProcessManager\ReservationAcceptedByInventoryProcessManager;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ReservationAcceptedByInventoryProcessManagerTest extends TestCase
{
    private ReservationAcceptedByInventoryProcessManager $processManager;
    /**
     * @var CommandIdGeneratorInterface&MockObject
     */
    private CommandIdGeneratorInterface $commandIdGenerator;

    /**
     * @var CreateDepositPaymentSessionHandlerInterface&MockObject
     */
    private CreateDepositPaymentSessionHandlerInterface $createDepositPaymentSessionHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandIdGenerator = $this->createMock(CommandIdGeneratorInterface::class);
        $this->createDepositPaymentSessionHandler = $this->createMock(CreateDepositPaymentSessionHandlerInterface::class);

        $this->processManager = new ReservationAcceptedByInventoryProcessManager(
            $this->commandIdGenerator,
            $this->createDepositPaymentSessionHandler,
        );
    }

    /**
     * @dataProvider provideReservationEvents
     */
    public function testHandle(EventDTO $event, CreateDepositPaymentSession $command): void
    {
        $this->createDepositPaymentSessionHandler
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
        $this->createDepositPaymentSessionHandler
            ->expects($this->never())
            ->method('handle');

        $this->commandIdGenerator
            ->expects($this->never())
            ->method('generate');

        $this->processManager->handle($event);
    }

    public function provideReservationEvents(): Generator
    {
        $reservationId = '1111-2222-3333';
        $unitIds = [1, 2, 3];

        yield 'ReservationAcceptedByInventory Event with full payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ReservationManagement.ReservationAcceptedByInventory',
                [
                    'reservation_id' => $reservationId,
                    'unit_ids' => $unitIds,
                ],
                []
            ),
            new CreateDepositPaymentSession(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                ReservationId::fromString($reservationId),
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
