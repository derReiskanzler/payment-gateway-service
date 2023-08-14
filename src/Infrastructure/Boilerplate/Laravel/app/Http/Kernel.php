<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\DeprecationResponse;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\EncryptCookies;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\RedirectIfAuthenticated;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\SuppressExceptions;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\ThrottleRequests;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyCsrfToken;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\VerifyJWK;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJwtUserId;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyStripeWebhookSignature\VerifyWebhookSignature;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyUuid;
use Allmyhomes\TokenVerification\Http\Middleware\ScopeValidationMiddleware;
use Barryvdh\Cors\HandleCors;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, string>
     */
    protected $middleware = [
        SuppressExceptions::class,
        HandleCors::class,
        CheckForMaintenanceMode::class,
    ];

    /**
     * @var array<int, string>
     */
    protected $middlewarePriority = [
        ScopeValidationMiddleware::class,
        VerifyUuid::class,
        VerifyJwtUserId::class,
        Authenticate::class,
        RefreshToken::class,
        DeprecationResponse::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<string>>
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],

        'api' => [
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, string>
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'bindings' => SubstituteBindings::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'throttle' => ThrottleRequests::class,

        'deprecate.response' => DeprecationResponse::class,
        'jwt.auth' => Authenticate::class,
        'jwt.refresh' => RefreshToken::class,
        'jwt.user.validate' => VerifyJwtUserId::class,
        'uuid.validate' => VerifyUuid::class,

        'verify-jwk' => VerifyJWK::class,

        'verify-webhook-signature' => VerifyWebhookSignature::class,
    ];
}
