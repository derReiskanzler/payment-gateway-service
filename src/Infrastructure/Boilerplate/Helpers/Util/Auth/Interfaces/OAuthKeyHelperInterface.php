<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces;

/**
 * Interface OAuthKeyHelperInterface.
 */
interface OAuthKeyHelperInterface
{
    /**
     * Get value from a specific file or environment variable dependant on the environment.
     *
     * try to get the key from the provided file (with prefix "test-" in testing environment)
     *  - if file not exist try to get the key from the provided environment-variable
     *  - if the environment-variable doesn't exist, just return an empty string
     */
    public static function get(string $environment, string $folderPath, string $fileName, string $envKey): string;
}
