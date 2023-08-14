<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response;

class ValidationError
{
    private string $message;

    private string $field;

    /**
     * Gets validation error form field.
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Sets validation error form field.
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * Gets message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Sets message.
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
