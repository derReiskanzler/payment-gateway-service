<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer;

use Illuminate\Contracts\Container\BindingResolutionException;

final class DocumentStateTranslatorFactory
{
    /**
     * @throws BindingResolutionException if nothing is bound to DocumentStateTranslatorInterface
     */
    public static function make(): DocumentStateTranslatorInterface
    {
        return app()->make(DocumentStateTranslatorInterface::class);
    }
}
