<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Extenders;

use Illuminate\Contracts\Foundation\Application;

trait InteractsWithLaravelApplication
{
    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    protected function app(): Application
    {
        return $this->app;
    }
}
