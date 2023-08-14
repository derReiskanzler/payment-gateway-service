<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenDepositPaymentEmailShouldNotExistTrait
{
    final protected function thenDepositPaymentEmailShouldNotExist(
        string $id,
        string $prospectId,
        string $checkoutSessionId,
        string $requestId,
        int $errorCount = 0,
    ): void {
        $this->assertDatabaseMissing(
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

    abstract public function assertDatabaseMissing(string $string, array $array);
}
