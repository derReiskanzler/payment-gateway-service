<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

class ManagerFactory
{
    public static function initialize(): Manager
    {
        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer());

        return $fractal;
    }
}
