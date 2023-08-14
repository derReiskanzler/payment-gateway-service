<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response;

class MultipleErrorResponse extends ErrorResponse
{
    /**
     * ['id' => ['id is short'], 'message' => ['message min 6 chars']].
     *
     * @param array<string, array<string>> $errors List of errors
     */
    public function __construct(string $message, int $errorCode, array $errors)
    {
        parent::__construct($message, $errorCode);

        $this->setErrorMessages($errors);
    }

    /**
     * Sets error messages.
     *
     * @param array<string, array<string>> $errors List of errors
     */
    private function setErrorMessages(array $errors): void
    {
        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $this->createError($message, $field);
            }
        }
    }

    /**
     * Adds the error to the Response.
     *
     * @param string|null $source Error source (field, file)
     */
    private function createError(string $message, ?string $source = null): void
    {
        $error = new ValidationError();

        if (null !== $source) {
            $error->setField($source);
        }
        $error->setMessage($message);

        $this->errors[] = $error;
    }
}
