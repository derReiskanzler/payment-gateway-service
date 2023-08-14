<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenUnitShouldNotExistTrait
{
    final protected function thenUnitShouldNotExist(int $id, string $unitName): void
    {
        $this->assertDatabaseMissing(
            'units',
            [
                'id' => $id,
                'doc->id' => $id,
                'doc->name' => $unitName,
            ]
        );
    }

    abstract public function assertDatabaseMissing(string $string, array $array);
}
