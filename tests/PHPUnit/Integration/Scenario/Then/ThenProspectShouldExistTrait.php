<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenProspectShouldExistTrait
{
    final protected function thenProspectShouldExist(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->assertDatabaseHas(
            'prospects',
            [
                'id' => $prospectId,
                'doc->id' => $prospectId,
                'doc->email' => $email,
                'doc->first_name' => $firstName,
                'doc->last_name' => $lastName,
                'doc->salutation' => $salutation,
            ]
        );
    }

    abstract public function assertDatabaseHas(string $string, array $array);
}
