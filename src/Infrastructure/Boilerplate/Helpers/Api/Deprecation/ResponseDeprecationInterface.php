<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation;

use Symfony\Component\HttpFoundation\Response;

interface ResponseDeprecationInterface
{
    public function deprecate(Response $response): Response;
}
