<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\PopulateProspect;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectProfileCreatedInKeycloakAdapterTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectProfileCreatedTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenProspectShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenProspectShouldNotExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheProspectProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class ProspectProfileCreatedUpsertsProspectTest extends IntegrationTestCase
{
    use GivenProspectProfileCreatedTrait;
    use GivenProspectProfileCreatedInKeycloakAdapterTrait;
    use GivenProspectExistsTrait;
    use WhenTheProspectProjectionRunsTrait;
    use ThenProspectShouldExistTrait;
    use ThenProspectShouldNotExistTrait;

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideProspectProfileCreatedData
     */
    public function testUpsert(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->givenProspectProfileCreated($prospectId, $email, $firstName, $lastName, $salutation);
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
     * @dataProvider provideProspectProfileCreatedData
     */
    public function testUpsertFromKeycloakAdapter(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->givenProspectProfileCreatedInKeycloakAdapter($prospectId, $email, $firstName, $lastName, $salutation);
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
     * @dataProvider provideProspectProfileCreatedData
     */
    public function testUpdate(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $updatedEmail = $this->faker()->email;
        $updatedFirstName = $this->faker()->email;
        $updatedLastName = $this->faker()->email;

        $this->givenProspectExists($prospectId, $email, $firstName, $lastName, $salutation);
        $this->givenProspectProfileCreated($prospectId, $updatedEmail, $updatedFirstName, $updatedLastName, $salutation);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldExist(
            $prospectId,
            $updatedEmail,
            $updatedFirstName,
            $updatedLastName,
            $salutation,
        );
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
     * @dataProvider provideProspectProfileCreatedData
     */
    public function testUpdateFromKeycloakAdapter(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $updatedEmail = $this->faker()->email;
        $updatedFirstName = $this->faker()->email;
        $updatedLastName = $this->faker()->email;

        $this->givenProspectExists($prospectId, $email, $firstName, $lastName, $salutation);
        $this->givenProspectProfileCreatedInKeycloakAdapter($prospectId, $updatedEmail, $updatedFirstName, $updatedLastName, $salutation);
        $this->whenTheProspectProjectionRuns();
        $this->thenProspectShouldExist(
            $prospectId,
            $updatedEmail,
            $updatedFirstName,
            $updatedLastName,
            $salutation,
        );
        $this->thenProspectShouldNotExist(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
    }

    public function provideProspectProfileCreatedData(): Generator
    {
        $prospectId = $this->faker()->uuid;
        $email = $this->faker()->email;
        $firstName = $this->faker()->firstName;
        $lastName = $this->faker()->lastName;
        $salutation = $this->faker()->numberBetween(0, 2);

        yield 'ProspectProfileCreated event data' => [
            'prospect_id' => $prospectId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'salutation' => $salutation,
        ];
    }
}
