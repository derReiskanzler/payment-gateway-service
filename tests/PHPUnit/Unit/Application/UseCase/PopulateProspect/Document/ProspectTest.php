<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\PopulateProspect\Document;

use Allmyhomes\Application\UseCase\PopulateProspect\Document\Prospect;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Generator;
use Tests\TestCase;

final class ProspectTest extends TestCase
{
    /**
     * @param array<string, mixed> $prospectData
     *
     * @dataProvider getProspectData
     */
    public function testId(array $prospectData): void
    {
        $prospect = Prospect::fromArray($prospectData);
        $this->assertEquals(
            $prospectData['id'],
            $prospect->id()->toString(),
            'id does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $prospectData
     *
     * @dataProvider getProspectData
     */
    public function testEmail(array $prospectData): void
    {
        $prospect = Prospect::fromArray($prospectData);
        $this->assertEquals(
            $prospectData['email'],
            $prospect->email()->toString(),
            'email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $prospectData
     *
     * @dataProvider getProspectData
     */
    public function testFirstName(array $prospectData): void
    {
        $prospect = Prospect::fromArray($prospectData);
        $this->assertEquals(
            $prospectData['first_name'],
            $prospect->firstName()?->toString(),
            'first name does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $prospectData
     *
     * @dataProvider getProspectData
     */
    public function testLastName(array $prospectData): void
    {
        $prospect = Prospect::fromArray($prospectData);
        $this->assertEquals(
            $prospectData['last_name'],
            $prospect->lastName()->toString(),
            'last name does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $prospectData
     *
     * @dataProvider getProspectData
     */
    public function testSalutation(array $prospectData): void
    {
        $prospect = Prospect::fromArray($prospectData);
        $this->assertEquals(
            $prospectData['salutation'],
            $prospect->salutation()?->toInt(),
            'salutation does not match expected int.'
        );
    }

    /**
     * @param array<string, mixed> $prospectData
     *
     * @dataProvider getProspectData
     */
    public function testToArray(array $prospectData): void
    {
        $prospect = new Prospect(
            ProspectId::fromString($prospectData['id']),
            ProspectEmail::fromString($prospectData['email']),
            ProspectFirstName::fromString($prospectData['first_name']),
            ProspectLastName::fromString($prospectData['last_name']),
            ProspectSalutation::fromInt($prospectData['salutation']),
        );

        $this->assertEquals(
            $prospectData,
            $prospect->toArray(),
            'prospect data does not match expected array',
        );
    }

    /**
     * @param array<string, mixed> $prospectData
     *
     * @dataProvider getProspectData
     */
    public function testFromArray(array $prospectData): void
    {
        $fromProspect = Prospect::fromArray($prospectData);

        $this->assertInstanceOf(
            Prospect::class,
            $fromProspect,
            'created prospect is not instance of expected class: Prospect.'
        );
    }

    /**
     * @return Generator<string, mixed>
     */
    public function getProspectData(): Generator
    {
        yield 'Prospect with full payload' => [
            'prospect_data' => [
                'id' => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                'email' => 'max.mustermann@gmail.com',
                'first_name' => 'Max',
                'last_name' => 'Mustermann',
                'salutation' => 0,
            ],
        ];
    }
}
