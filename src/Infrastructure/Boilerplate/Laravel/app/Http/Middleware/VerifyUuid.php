<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VerifyUuid
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request Request
     * @param Closure $next    Closure
     *
     * @throws \JsonException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $route = $request->route();
        if ($route instanceof Route
            && $route->hasParameter('id')
            && \is_string($route->parameter('id'))
            && ($jsonMessage = json_encode(['id' => ['The route URL id parameter is not correct.']], \JSON_THROW_ON_ERROR))
            && !Uuid::isValid($route->parameter('id'))
        ) {
            throw new BadRequestHttpException($jsonMessage);
        }

        return $next($request);
    }
}
