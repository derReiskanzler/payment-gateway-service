<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheProspectProjectionRunsTrait;

trait GivenProspectExistsTrait
{
    use GivenProspectProfileCreatedTrait;
    use WhenTheProspectProjectionRunsTrait;

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final public function givenProspectExists(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->givenProspectProfileCreated(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
        $this->whenTheProspectProjectionRuns();
    }
}
