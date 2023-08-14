<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests as LaravelThrottleRequests;
use Illuminate\Routing\Route;
use RuntimeException;

class ThrottleRequests extends LaravelThrottleRequests
{
    /**
     * Check Request Signature using combination of route methods, uri, domain and IP Address.
     *
     * @param Request $request Request
     */
    protected function resolveRequestSignature($request): string
    {
        $route = $request->route();
        if ($route instanceof Route) {
            return sha1(
                implode('|', $route->methods()).
                '|'.$route->getDomain().
                '|'.$route->uri().
                '|'.$request->ip()
            );
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }
}
