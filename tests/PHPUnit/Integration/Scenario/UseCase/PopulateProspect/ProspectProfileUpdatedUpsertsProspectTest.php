<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\PopulateProspect;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectProfileUpdatedInKeycloakAdapterTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectProfileUpdatedTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenProspectShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenProspectShouldNotExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheProspectProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class ProspectProfileUpdatedUpsertsProspectTest extends IntegrationTestCase
{
    use GivenProspectProfileUpdatedTrait;
    use GivenProspectProfileUpdatedInKeycloakAdapterTrait;
    use GivenProspectExistsTrait;
    use WhenTheProspectProjectionRunsTrait;
    use ThenProspectShouldExistTrait;
    use ThenProspectShouldNotExistTrait;

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideProspectProfileUpdatedData
     */
    public function testCreate(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->givenProspectProfileUpdated($prospectId, $email, $firstName, $lastName, $salutation);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldExist(
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
     * @dataProvider provideProspectProfileUpdatedData
     */
    public function testCreateFromKeycloakAdapter(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->givenProspectProfileUpdatedInKeycloakAdapter($prospectId, $email, $firstName, $lastName, $salutation);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldExist(
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
     * @dataProvider provideProspectProfileUpdatedData
     */
    public function testUpdate(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $existingEmail = $this->faker()->email;
        $existingFirstName = $this->faker()->email;
        $existingLastName = $this->faker()->email;

        $this->givenProspectExists($prospectId, $existingEmail, $existingFirstName, $existingLastName, $salutation);
        $this->givenProspectProfileUpdated($prospectId, $email, $firstName, $lastName, $salutation);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldExist(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
        $this->thenProspectShouldNotExist(
            $prospectId,
            $existingEmail,
            $existingFirstName,
            $existingLastName,
            $salutation,
        );
    }

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideProspectProfileUpdatedData
     */
    public function testUpdateFromKeycloakAdapter(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $existingEmail = $this->faker()->email;
        $existingFirstName = $this->faker()->email;
        $existingLastName = $this->faker()->email;

        $this->givenProspectExists($prospectId, $existingEmail, $existingFirstName, $existingLastName, $salutation);
        $this->givenProspectProfileUpdatedInKeycloakAdapter($prospectId, $email, $firstName, $lastName, $salutation);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldExist(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
        $this->thenProspectShouldNotExist(
            $prospectId,
            $existingEmail,
            $existingFirstName,
            $existingLastName,
            $salutation,
        );
    }

    public function provideProspectProfileUpdatedData(): Generator
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
