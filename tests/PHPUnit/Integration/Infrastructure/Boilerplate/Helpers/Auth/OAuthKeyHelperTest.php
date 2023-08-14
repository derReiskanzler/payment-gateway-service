<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Helpers\Auth;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Helpers\OAuthKeyHelper;
use PHPUnit\Framework\TestCase;

class OAuthKeyHelperTest extends TestCase
{
    /**
     * Get OAuth key with OAuthKeyHelper.
     *
     * @param array<string, array<string, string>> $dataProvider Data provider
     * @dataProvider getOAuthKeyDataProvider
     */
    public function testGetOAuthKey(array $dataProvider): void
    {
        /**
         * @var string $expected
         */
        $expected = $dataProvider['expected'];
        $environmentVariable = $dataProvider['data']['environmentVariable'];
        $environment = $dataProvider['data']['environment'];
        if (!isset($dataProvider['no_env'])) {
            $this->setTestEnvironment($environmentVariable, $environment);
        }

        $value = OAuthKeyHelper::get($environment, $dataProvider['data']['folder_path'], $dataProvider['data']['file_name'], $environmentVariable);
        static::assertSame($expected, $value);
    }

    /**
     * Data provider for `testGetOAuthKey`.
     *
     * @return array<int, array<int, array<string, bool|string|array<string, string>>>>
     */
    public function getOAuthKeyDataProvider(): array
    {
        $file = [
            'exists' => [
                'folder_path' => 'tests/storage/',
                'file_prefix' => 'test-',
                'file_name' => 'public.key',
            ],
            'not_exists' => [
                'folder_path' => 'tests/storage/',
                'file_name' => 'file-not-exists.key',
            ],
        ];

        $env = [
            'exists' => [
                'environment' => 'testing',
                'environmentVariable' => 'FAKE_AUTH_KEY_FOR_UNIT_TEST',
            ],
            'not_exists' => [
                'environment' => 'testing',
                'environmentVariable' => 'KEY_NOT_EXISTS',
            ],
        ];

        $data = [
            'file_exists_env_exists' => array_merge($file['exists'], $env['exists']),
            'file_exists_env_not_exists' => array_merge($file['exists'], $env['not_exists']),
            'file_not_exists_env_exists' => array_merge($file['not_exists'], $env['exists']),
            'file_and_env_not_exist' => array_merge($file['not_exists'], $env['not_exists']),
        ];

        $expected = [
            'file' => file_get_contents($file['exists']['folder_path'].$file['exists']['file_prefix'].$file['exists']['file_name']),
            'variable' => $env['exists']['environment'],
            'empty_string' => '',
        ];

        return [
            [[
                'case' => 'get oauth-key from the file as it exists and has higher priority than an environment variable',
                'data' => $data['file_exists_env_exists'],
                'expected' => $expected['file'],
            ]],

            [[
                'case' => 'get oauth-key from the file since env variable does not exist',
                'no_env' => true,
                'data' => $data['file_exists_env_not_exists'],
                'expected' => $expected['file'],
            ]],

            [[
                'case' => 'get oauth-key from env variable since the file does not exist and env exists',
                'data' => $data['file_not_exists_env_exists'],
                'expected' => $expected['variable'],
            ]],

            [[
                'case' => 'both file and env do not exist',
                'no_env' => true,
                'data' => $data['file_and_env_not_exist'],
                'expected' => $expected['empty_string'],
            ]],
        ];
    }

    private function setTestEnvironment(string $environmentVariable, string $value): void
    {
        putenv($environmentVariable.'='.$value);
    }
}
