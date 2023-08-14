<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\PopulateUnit;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenPlatformUnitContentPublishedTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenUnitExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenUnitShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenUnitShouldNotExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheUnitProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class PlatformUnitContentPublishedUpsertsUnitTest extends IntegrationTestCase
{
    use GivenPlatformUnitContentPublishedTrait;
    use GivenUnitExistsTrait;
    use WhenTheUnitProjectionRunsTrait;
    use ThenUnitShouldExistTrait;
    use ThenUnitShouldNotExistTrait;

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    public function testUpsert(): void
    {
        $unitId = $this->faker()->numberBetween(1, 1000000);
        $unitName = $this->faker()->text(10);

        $this->givenPlatformUnitContentPublished($unitId, $unitName);
        $this->whenTheUnitProjectionRuns();
        $this->thenUnitShouldExist($unitId, $unitName);
    }

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    public function testUpdate(): void
    {
        $unitId = $this->faker()->numberBetween(1, 1000000);
        $unitName = $this->faker()->text(10);
        $updatedUnitName = $this->faker()->text(10);

        $this->givenUnitExists($unitId, $unitName);
        $this->givenPlatformUnitContentPublished($unitId, $updatedUnitName);
        $this->whenTheUnitProjectionRuns();
        $this->thenUnitShouldExist($unitId, $updatedUnitName);
    }

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    public function testSkipNonGermanUnitContent(): void
    {
        $languageCode = 'en-US';
        $unitId = $this->faker()->numberBetween(1, 1000000);
        $unitName = $this->faker()->text(10);

        $this->givenPlatformUnitContentPublished($unitId, $unitName, $languageCode);
        $this->whenTheUnitProjectionRuns();
        $this->thenUnitShouldNotExist($unitId, $unitName);
    }
}
