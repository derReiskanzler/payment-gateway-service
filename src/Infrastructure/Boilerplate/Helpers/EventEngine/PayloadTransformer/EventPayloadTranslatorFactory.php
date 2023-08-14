<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer;

use Illuminate\Contracts\Container\BindingResolutionException;

class EventPayloadTranslatorFactory
{
    /**
     * @throws BindingResolutionException if nothing is bound to EventPayloadTranslatorInterface
     */
    public static function make(): EventPayloadTranslatorInterface
    {
        return app()->make(EventPayloadTranslatorInterface::class);
    }
}
