<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware;

use Allmyhomes\TokenVerification\Enum\JWTPayloadIndex;
use Allmyhomes\TokenVerification\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VerifyJwtUserId
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (null === $request->get(TokenService::JWT_PAYLOAD_INDEX, null)) {
            $this->throwAccessDeniedException();
        }

        $this->validateUserIdWithJwtToken($request);

        return $next($request);
    }

    private function throwAccessDeniedException(): void
    {
        throw new AccessDeniedHttpException("You don't have access to this resource.");
    }

    /**
     * @param Request $request Request
     */
    private function validateUserIdWithJwtToken(Request $request): void
    {
        $userId = !empty($request->get(TokenService::JWT_PAYLOAD_INDEX)[JWTPayloadIndex::USER_ID])
            ? $request->get(TokenService::JWT_PAYLOAD_INDEX)[JWTPayloadIndex::USER_ID]
            : null;

        $route = $request->route();
        if ($route instanceof Route && $route->hasParameter('id') && $userId !== $route->parameter('id')) {
            $this->throwAccessDeniedException();
        }
    }
}
