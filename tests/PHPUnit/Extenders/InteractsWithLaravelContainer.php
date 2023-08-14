<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Extenders;

use Illuminate\Contracts\Container\BindingResolutionException;

trait InteractsWithLaravelContainer
{
    use InteractsWithLaravelApplication;

    /**
     * Helper method to create a service by name and with provided parameters.
     *
     * @param array<mixed> $parameters
     *
     * @throws BindingResolutionException
     */
    protected function service(string $serviceName, array $parameters = []): mixed
    {
        return $this->app()->make($serviceName, $parameters);
    }
}
