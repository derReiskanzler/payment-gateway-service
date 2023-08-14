<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Request;

use Allmyhomes\DDDAbstractions\Application\AbstractRequest;

final class CompleteDepositPaymentSessionRequest extends AbstractRequest
{
    public const DATA = 'data';
    public const OBJECT = 'object';
    public const CHECKOUT_SESSION_ID = 'id';
    public const CHECKOUT_SESSION_STATUS = 'status';
    public const PAYMENT_STATUS = 'payment_status';
    public const METADATA = 'metadata';
    public const RESERVATION_ID = 'reservation_id';
    public const PROSPECT_ID = 'prospect_id';
    public const PROJECT_ID = 'project_id';
    public const AGENT_ID = 'agent_id';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            self::DATA => ['required', 'array'],
            sprintf('%s.%s', self::DATA, self::OBJECT) => ['required', 'array'],
            sprintf('%s.%s.%s', self::DATA, self::OBJECT, self::CHECKOUT_SESSION_ID) => ['required', 'string'],
            sprintf('%s.%s.%s', self::DATA, self::OBJECT, self::CHECKOUT_SESSION_STATUS) => ['required', 'string'],
            sprintf('%s.%s.%s', self::DATA, self::OBJECT, self::PAYMENT_STATUS) => ['required', 'string'],
            sprintf('%s.%s.%s', self::DATA, self::OBJECT, self::METADATA) => ['required', 'array'],
            sprintf('%s.%s.%s.%s', self::DATA, self::OBJECT, self::METADATA, self::RESERVATION_ID) => ['required', 'string'],
            sprintf('%s.%s.%s.%s', self::DATA, self::OBJECT, self::METADATA, self::PROSPECT_ID) => ['required', 'string'],
            sprintf('%s.%s.%s.%s', self::DATA, self::OBJECT, self::METADATA, self::PROJECT_ID) => ['required', 'integer'],
            sprintf('%s.%s.%s.%s', self::DATA, self::OBJECT, self::METADATA, self::AGENT_ID) => ['required', 'string'],
        ];
    }

    public function reservationId(): string
    {
        return $this->getObject()[self::METADATA][self::RESERVATION_ID];
    }

    public function checkoutSessionId(): string
    {
        return $this->getObject()[self::CHECKOUT_SESSION_ID];
    }

    public function checkoutSessionStatus(): string
    {
        return $this->getObject()[self::CHECKOUT_SESSION_STATUS];
    }

    public function paymentStatus(): string
    {
        return $this->getObject()[self::PAYMENT_STATUS];
    }

    /**
     * @return array<string, mixed>
     */
    private function getObject(): array
    {
        return $this->all()[self::DATA][self::OBJECT];
    }
}
