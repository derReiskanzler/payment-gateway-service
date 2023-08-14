<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationValidators\FullDeprecationHeaderValidator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseDeprecation
{
    /**
     * @throws BindingResolutionException if nothing is bound to LoggerInterface
     */
    public function deprecate(
        Request $request,
        Response $response,
        DeprecationConfigurationInterface $deprecationConfiguration
    ): Response {
        $responseDeprecation = new LogDeprecationHeader(
            responseDeprecation: new AddDeprecationHeader($deprecationConfiguration, new FullDeprecationHeaderValidator()),
            request: $request,
            logger: app()->make(LoggerInterface::class)
        );

        return $responseDeprecation->deprecate($response);
    }
}
