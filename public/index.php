<?php

declare(strict_types=1);

/**
 * Laravel - A PHP Framework For Web Artisans.
 *
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels nice to relax.
|
*/
$C3 = __DIR__.'/../c3.php';
if (file_exists($C3)) {
    // Optional (if not set the default c3 output dir will be used)
    define('C3_CODECOVERAGE_ERROR_LOG_FILE', __DIR__.'/../Application/storage/logs/c3_error.log');
    include $C3;
}

define('MY_APP_STARTED', true);

require __DIR__.'/../src/Infrastructure/Boilerplate/Laravel/bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

/** @var Allmyhomes\Infrastructure\Boilerplate\Laravel\Bootstrap $app */
$app = require __DIR__.'/../src/Infrastructure/Boilerplate/Laravel/bootstrap/app.php';

/* Support logging on fatals */
require_once __DIR__.'/../src/Infrastructure/Boilerplate/Laravel/app/Logging/logger.php';

register_shutdown_function('logOnShutdown');

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

/** @var Illuminate\Contracts\Http\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = Illuminate\Http\Request::capture();

/** @var \Symfony\Component\HttpFoundation\Response $response */
$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
