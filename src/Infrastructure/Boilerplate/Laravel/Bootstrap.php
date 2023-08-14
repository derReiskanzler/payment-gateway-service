<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel;

use Illuminate\Foundation\Application;

class Bootstrap extends Application
{
    /**
     * App path.
     */
    public function appPath(): string
    {
        return $this->basePath.\DIRECTORY_SEPARATOR.'Application';
    }

    /**
     * Infrastructure path.
     */
    public function infrastructurePath(): string
    {
        return $this->basePath.\DIRECTORY_SEPARATOR.'Infrastructure';
    }

    /**
     * Domain path.
     */
    public function domainPath(): string
    {
        return $this->basePath.\DIRECTORY_SEPARATOR.'Domain';
    }

    /**
     * App path.
     */
    public function laravelPath(): string
    {
        return __DIR__;
    }

    /**
     * Routes path.
     */
    public function routesPath(): string
    {
        return $this->laravelPath().\DIRECTORY_SEPARATOR.'routes';
    }

    /**
     * Config path.
     *
     * @param string $path
     */
    public function configPath($path = ''): string
    {
        return $this->laravelPath().\DIRECTORY_SEPARATOR.'config'.($path ? \DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Bootstrap path.
     *
     * @param string $path
     */
    public function bootstrapPath($path = ''): string
    {
        return $this->laravelPath().\DIRECTORY_SEPARATOR.'bootstrap'.($path ? \DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Storage path.
     */
    public function storagePath(): string
    {
        return $this->storagePath ?: $this->laravelPath().\DIRECTORY_SEPARATOR.'storage';
    }

    /**
     * Resource path.
     *
     * @param string $path Path
     */
    public function resourcePath($path = ''): string
    {
        return $this->laravelPath().\DIRECTORY_SEPARATOR.'resources'.($path ? \DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Tests path.
     */
    public function testsPath(): string
    {
        return $this->basePath.\DIRECTORY_SEPARATOR.'tests';
    }

    /**
     * Database path.
     *
     * @param string $path Path
     */
    public function databasePath($path = ''): string
    {
        return $this->basePath.\DIRECTORY_SEPARATOR.'database'
            .($path ? \DIRECTORY_SEPARATOR.$path : $path);
    }
}
