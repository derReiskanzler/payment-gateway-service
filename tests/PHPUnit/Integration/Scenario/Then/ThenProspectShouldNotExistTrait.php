<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenProspectShouldNotExistTrait
{
    final protected function thenProspectShouldNotExist(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        $this->assertDatabaseMissing(
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

    abstract public function assertDatabaseMissing(string $string, array $array);
}
