<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

use Allmyhomes\Infrastructure\Boilerplate\Laravel\Bootstrap;

$app = new Bootstrap(
    dirname(__DIR__, 5).'/'
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Handler::class
);

$app->bind(
    Ramsey\Uuid\UuidFactoryInterface::class,
    Ramsey\Uuid\UuidFactory::class
);

$app->bind(
    Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface::class,
    Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGenerator::class
);

$app->bind(
    Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\ConfigFactories\AbstractVerifyJWKConfigFactory::class,
    Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\ConfigFactories\VerifyJWKConfigFactory::class
);

/* Providers */
$app->register(Allmyhomes\TokenVerification\Provider\ScopeServiceProvider::class);
$app->register(Allmyhomes\Infrastructure\ServiceProvider\DepositPaymentSessionServiceProvider::class);
$app->register(Allmyhomes\Infrastructure\ServiceProvider\PaymentGatewayServiceProvider::class);
$app->register(Allmyhomes\Infrastructure\ServiceProvider\DepositPaymentEmailServiceProvider::class);
$app->register(Allmyhomes\Infrastructure\ServiceProvider\MailRendererServiceProvider::class);
$app->register(Allmyhomes\Infrastructure\ServiceProvider\CompleteDepositPaymentSessionServiceProvider::class);


/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
