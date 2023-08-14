<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenUnitShouldExistTrait
{
    final protected function thenUnitShouldExist(int $id, string $unitName): void
    {
        $this->assertDatabaseHas(
            'units',
            [
                'id' => $id,
                'doc->id' => $id,
                'doc->name' => $unitName,
            ]
        );
    }

    abstract public function assertDatabaseHas(string $string, array $array);
}
