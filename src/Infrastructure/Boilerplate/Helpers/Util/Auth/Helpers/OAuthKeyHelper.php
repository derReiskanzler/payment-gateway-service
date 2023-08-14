<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Helpers;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces\OAuthKeyHelperInterface;

class OAuthKeyHelper implements OAuthKeyHelperInterface
{
    private const TESTING_ENVIRONMENT = 'testing';
    /**
     * File name prefix for testing environment.
     */
    public static string $testEnvironmentPrefix = 'test-';

    /**
     * Get value from a specific file or environment variable dependent on the environment.
     *
     * try to get the key from the provided file (with prefix "test-" in testing environment)
     *  - if file not exist try to get the key from the provided environment-variable
     *  - if the environment-variable doesn't exist, just return an empty string
     */
    public static function get(string $environment, string $folderPath, string $fileName, string $envKey): string
    {
        $fileContent = self::getFromFile($environment, $folderPath, $fileName);
        if (!empty($fileContent)) {
            return $fileContent;
        }

        if (!empty(env($envKey))) {
            return env($envKey);
        }

        return '';
    }

    /**
     * Get value from a specific file.
     */
    protected static function getFromFile(string $environment, string $folderPath, string $fileName): ?string
    {
        $prefix = (self::TESTING_ENVIRONMENT === $environment) ? self::$testEnvironmentPrefix : '';
        $filePath = $folderPath.\DIRECTORY_SEPARATOR.$prefix.$fileName;

        return (file_exists($filePath) && false !== file_get_contents($filePath)) ? file_get_contents($filePath) : null;
    }
}
