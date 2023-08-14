<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuppressExceptions
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        error_reporting((int) config('app.error_level'));

        return $next($request);
    }
}
