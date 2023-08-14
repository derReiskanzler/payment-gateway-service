<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenDepositPaymentSessionShouldExistTrait
{
    final protected function thenDepositPaymentSessionShouldExist(
        string $id,
        string $checkoutSessionId = null,
        int $errorCount = 0,
        string $status = null,
        string $paymentStatus = null,
    ): void {
        $this->assertDatabaseHas(
    'deposit_payment_session',
            [
                'id' => $id,
                'doc->state->reservation_id' => $id,
                'doc->state->checkout_session_id' => $checkoutSessionId,
                'doc->state->error_count' => $errorCount,
                'doc->state->checkout_session_status' => $status,
                'doc->state->payment_status' => $paymentStatus,
            ],
        );
    }

    abstract public function assertDatabaseHas(string $string, array $array);
}
