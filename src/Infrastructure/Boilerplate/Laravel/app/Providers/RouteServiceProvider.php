<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers\CatchRouteNotAvailableController;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers\HomeController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Index file for Boilerplate API routes.
     */
    private const BOILERPLATE_API_ROUTE = 'api.php';

    /**
     * Index file for API routes.
     */
    private const APPLICATION_API_ROUTE = 'Inbound/Api/Route/api.php';
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = '';

    protected string $routesPath = '';

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->initialize();

        $this->mapBoilerplateRoutes();

        $this->mapApplicationRoutes();

        $this->mapWebRoutes();

        $this->handleNotFoundRoutes();
    }

    protected function initialize(): void
    {
        $this->routesPath = app()->routesPath();
    }

    protected function mapWebRoutes(): void
    {
        Route::get('/', [HomeController::class, 'index']);
    }

    protected function mapBoilerplateRoutes(): void
    {
        Route::prefix(config('api.prefix'))
            ->middleware('api')
            ->namespace($this->namespace)
            ->group($this->routesPath.\DIRECTORY_SEPARATOR.self::BOILERPLATE_API_ROUTE);
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApplicationRoutes(): void
    {
        Route::prefix(config('api.prefix'))
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(\dirname($this->routesPath, 3).\DIRECTORY_SEPARATOR.self::APPLICATION_API_ROUTE);
    }

    protected function handleNotFoundRoutes(): void
    {
        Route::prefix(config('api.prefix'))
            ->middleware('api')
            ->namespace($this->namespace)
            ->any('/{any}', [CatchRouteNotAvailableController::class, 'catchRoute'])
            ->where('any', '.*');
    }
}
