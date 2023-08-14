<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util;

use RuntimeException;

final class StringHelper
{
    public static function camelCaseToUnderscore(string $string): ?string
    {
        $string = preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $string);

        if (null === $string) {
            throw new RuntimeException('Unsupported string provided');
        }

        return strtolower($string);
    }
}
