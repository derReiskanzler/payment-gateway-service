<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\PopulateProspect\Projector;

use Allmyhomes\Application\UseCase\PopulateProspect\Document\Prospect;
use Allmyhomes\Application\UseCase\PopulateProspect\Projector\ProspectsProjector;
use Allmyhomes\Application\UseCase\PopulateProspect\Repository\ProspectRepositoryInterface;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ProspectsProjectorTest extends TestCase
{
    /**
     * @var MockObject&ProspectRepositoryInterface
     */
    private MockObject $repository;
    private ProspectsProjector $projector;

    public function setUp(): void
    {
        $this->repository = $this->createMock(ProspectRepositoryInterface::class);
        $this->projector = new ProspectsProjector($this->repository);
    }

    /**
     * @throws \Exception
     *
     * @dataProvider provideProspectEvents
     */
    public function testHandleProspectProfileEvent(EventDTO $event, Prospect $prospect): void
    {
        $this->repository
            ->expects($this->once())
            ->method('upsert')
            ->with($prospect);

        $this->projector->handle($event);
    }

    /**
     * @throws \Exception
     *
     * @dataProvider provideProspectProfileDeletedEvent
     */
    public function testHandleProspectProfileDeletedEvent(EventDTO $event): void
    {
        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(ProspectId::fromString($event->getPayload()['id']));

        $this->projector->handle($event);
    }

    /**
     * @throws \Exception
     *
     * @dataProvider provideOtherEvent
     */
    public function testHandleOtherEvent(EventDTO $event): void
    {
        $this->repository
            ->expects($this->never())
            ->method('upsert');

        $this->projector->handle($event);
    }

    /**
     * @return Generator<mixed>
     */
    public function provideProspectEvents(): Generator
    {
        $prospectId = 'ca50819f-e5a4-40d3-a425-daba3e095407';
        $email = 'max.mustermann@gmail.com';
        $firstName = 'Max';
        $lastName = 'Mustermann';
        $salutation = 0;

        yield 'ProspectProfileCreated with full payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'User.ProspectProfileCreated',
                [
                    'id' => $prospectId,
                    'email' => $email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'salutation' => $salutation,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                ],
                []
            ),
            new Prospect(
                ProspectId::fromString($prospectId),
                ProspectEmail::fromString($email),
                ProspectFirstName::fromString($firstName),
                ProspectLastName::fromString($lastName),
                ProspectSalutation::fromInt($salutation),
            ),
        ];

        yield 'ProspectProfileCreated without optionals' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'User.ProspectProfileCreated',
                [
                    'id' => $prospectId,
                    'email' => $email,
                    'last_name' => $lastName,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                ],
                []
            ),
            new Prospect(
                ProspectId::fromString($prospectId),
                ProspectEmail::fromString($email),
                null,
                ProspectLastName::fromString($lastName),
                null,
            ),
        ];

        yield 'ProspectProfileUpdated with full payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'User.ProspectProfileUpdated',
                [
                    'id' => $prospectId,
                    'email' => $email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'salutation' => $salutation,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                ],
                []
            ),
            new Prospect(
                ProspectId::fromString($prospectId),
                ProspectEmail::fromString($email),
                ProspectFirstName::fromString($firstName),
                ProspectLastName::fromString($lastName),
                ProspectSalutation::fromInt($salutation),
            ),
        ];

        yield 'ProspectProfileUpdated without optionals' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'User.ProspectProfileUpdated',
                [
                    'id' => $prospectId,
                    'email' => $email,
                    'last_name' => $lastName,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                ],
                []
            ),
            new Prospect(
                ProspectId::fromString($prospectId),
                ProspectEmail::fromString($email),
                null,
                ProspectLastName::fromString($lastName),
                null,
            ),
        ];
    }

    /**
     * @return Generator<mixed>
     */
    public function provideProspectProfileDeletedEvent(): Generator
    {
        $prospectId = 'ca50819f-e5a4-40d3-a425-daba3e095407';

        yield 'ProspectProfileUpdated without optionals' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'User.ProspectProfileDeleted',
                [
                    'id' => $prospectId,
                    'occurred_at' => '2020-06-27T21:37:45.531877',
                ],
                []
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
