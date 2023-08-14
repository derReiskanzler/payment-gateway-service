<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationValidators\DeprecationHeaderValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

class AddDeprecationHeader implements ResponseDeprecationInterface
{
    public function __construct(
        private DeprecationConfigurationInterface $deprecationConfiguration,
        private DeprecationHeaderValidatorInterface $deprecationHeaderValidator
    ) {
    }

    public function deprecate(Response $response): Response
    {
        $this->deprecationHeaderValidator->validate($this->deprecationConfiguration);

        $response->headers->add($this->addDeprecateHeader());
        $response->headers->add($this->addLinkHeader());
        $response->headers->add($this->addSunsetHeader());

        return $response;
    }

    /**
     * @return array<string, bool|string>
     */
    private function addDeprecateHeader(): array
    {
        return ['Deprecation' => $this->deprecationConfiguration->getDeprecation()];
    }

    /**
     * @return array<string, string>
     */
    private function addLinkHeader(): array
    {
        return ['Link' => sprintf('<%s>; rel="deprecation"; type="application/vnd.oai.openapi"', $this->deprecationConfiguration->getLink())];
    }

    /**
     * @return array<string, string>
     */
    private function addSunsetHeader(): array
    {
        return ['Sunset' => $this->deprecationConfiguration->getSunset()];
    }
}
