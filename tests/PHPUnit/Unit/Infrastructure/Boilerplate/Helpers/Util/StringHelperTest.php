<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Util;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    /**
     * Test camelCase to Underscore.
     *
     * @param array<string, string> $data data
     *
     * @dataProvider exceptionDataProvider
     */
    public function testCamelCaseToUnderscore(array $data): void
    {
        static::assertSame($data['expected'], StringHelper::camelCaseToUnderscore($data['original']));
    }

    /**
     * @return array<int, array<int, array<string, string>>>
     */
    public function exceptionDataProvider(): array
    {
        return [
            [[
                'original' => 'serviceboilerplateTest',
                'expected' => 'serviceboilerplate_test',
            ]],
            [[
                'original' => 'serviceBoilerplateTest',
                'expected' => 'service_boilerplate_test',
            ]],
        ];
    }
}
