<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\PopulateProspect;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectProfileDeletedInKeycloakAdapterTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectProfileDeletedTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenProspectShouldNotExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheProspectProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class ProspectProfileDeletedDeletesProspectTest extends IntegrationTestCase
{
    use GivenProspectProfileDeletedTrait;
    use GivenProspectProfileDeletedInKeycloakAdapterTrait;
    use GivenProspectExistsTrait;
    use WhenTheProspectProjectionRunsTrait;
    use ThenProspectShouldNotExistTrait;

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideProspectProfileDeletedData
     */
    public function testDelete(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->givenProspectExists(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
        $this->givenProspectProfileDeleted($prospectId);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldNotExist(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
    }

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideProspectProfileDeletedData
     */
    public function testDeleteFromKeycloakAdapter(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->givenProspectExists(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
        $this->givenProspectProfileDeletedInKeycloakAdapter($prospectId);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldNotExist(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
    }

    public function provideProspectProfileDeletedData(): Generator
    {
        $prospectId = $this->faker()->uuid;
        $email = $this->faker()->email;
        $firstName = $this->faker()->firstName;
        $lastName = $this->faker()->lastName;
        $salutation = $this->faker()->numberBetween(0, 2);

        yield 'ProspectProfileUpdated event data' => [
            'prospect_id' => $prospectId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'salutation' => $salutation,
        ];
    }
}
