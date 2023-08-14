<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenDepositPaymentEmailShouldExistTrait
{
    final protected function thenDepositPaymentEmailShouldExist(
        string $id,
        string $prospectId,
        string $checkoutSessionId,
        ?string $requestId,
        int $errorCount = 0,
    ): void {
        $this->assertDatabaseHas(
    'deposit_payment_email',
            [
                'id' => $id,
                'doc->state->reservation_id' => $id,
                'doc->state->prospect_id' => $prospectId,
                'doc->state->checkout_session_id' => $checkoutSessionId,
                'doc->state->request_id' => $requestId,
                'doc->state->error_count' => $errorCount,
            ],
        );
    }

    abstract public function assertDatabaseHas(string $string, array $array);
}
