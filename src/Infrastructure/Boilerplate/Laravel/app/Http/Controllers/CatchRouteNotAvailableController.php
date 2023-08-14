<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class CatchRouteNotAvailableController extends Controller
{
    /**
     * Catches route and throws NotAcceptableHttpException 406.
     */
    public function catchRoute(): void
    {
        throw new NotAcceptableHttpException('The requested url is not an acceptable route.');
    }
}
