<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Ramsey\Uuid\Uuid;

class UuidRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param mixed $attribute is ignored!
     */
    public function passes(mixed $attribute, mixed $value): bool
    {
        return Uuid::isValid($value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'validation.uuid-rule' !== trans('validation.uuid-rule') && \is_string(trans('validation.uuid-rule')) ? trans('validation.uuid-rule') : 'The :attribute must be a UUID.';
    }
}
