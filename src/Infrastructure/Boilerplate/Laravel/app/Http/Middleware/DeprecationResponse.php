<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfiguration;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\ResponseDeprecation;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use UnexpectedValueException;

class DeprecationResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request Request
     * @param Closure $next    Closure
     *
     * @throws BindingResolutionException
     * @throw UnexpectedValueException if Route not found or sunset date value invalid
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $route = $request->route();
        if ($route instanceof Route
            && ($deprecationRouteConfiguration = $route->getAction('deprecate.response'))
            && ($sunset = date_create_immutable($deprecationRouteConfiguration['sunset'])) instanceof DateTimeInterface
        ) {
            $deprecationConfiguration = new DeprecationConfiguration(
                $deprecationRouteConfiguration['deprecation'],
                $deprecationRouteConfiguration['link'],
                $sunset
            );
            $deprecateResponse = new ResponseDeprecation();
            $deprecateResponse->deprecate($request, $response, $deprecationConfiguration);

            return $response;
        }

        throw new UnexpectedValueException('Route not found or sunset date value invalid');
    }
}
