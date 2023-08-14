<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response;

use Illuminate\Http\Response;

interface ErrorResponseInterface
{
    /**
     * Gets response.
     */
    public function getResponse(): Response;
}
