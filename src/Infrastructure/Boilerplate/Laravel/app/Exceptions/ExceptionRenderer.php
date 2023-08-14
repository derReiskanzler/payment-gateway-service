<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\ValidHttpCodes;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response\ErrorResponse;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response\MultipleErrorResponse;
use Allmyhomes\LaravelKeycloakGuardPackage\Exceptions\KeycloakGuardException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Prooph\EventStore\Exception\ConcurrencyException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ExceptionRenderer
{
    /**
     * Builds custom Response for AMH services.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \JsonException
     */
    public function render(Request $request, Exception $exception): Response
    {
        $code = $this->getExceptionCode($exception);
        $defaultMessage = $this->setDefaultMessage($code);
        $message = !empty($exception->getMessage()) ? $exception->getMessage() : $defaultMessage;
        /** @noinspection JsonEncodingApiUsageInspection */
        $errors = json_decode(
            json: $message,
            associative: true,
            flags: \JSON_PARTIAL_OUTPUT_ON_ERROR
        );

        if (null !== $errors) {
            $response = new MultipleErrorResponse($defaultMessage, $code, (array) $errors);
        } else {
            $response = new ErrorResponse($message, $code);
        }

        return $response->getResponse();
    }

    /**
     * Get Exception code.
     */
    private function getExceptionCode(Exception $exception): int
    {
        $code = match (true) {
            $exception instanceof HttpException, $exception instanceof KeycloakGuardException => $exception->getStatusCode(),
            $exception instanceof ModelNotFoundException => Response::HTTP_NOT_FOUND,
            $exception instanceof InvalidArgumentException => Response::HTTP_BAD_REQUEST,
            $exception instanceof QueryException, $this->isPostgresRuntimeException($exception), $exception instanceof ConcurrencyException => Response::HTTP_UNPROCESSABLE_ENTITY,
            $exception instanceof TokenInvalidException => Response::HTTP_UNAUTHORIZED,
            default => $exception->getCode(),
        };

        return ValidHttpCodes::isValidHttpCode($code) ? $code : 500;
    }

    /**
     * Sets default Exception message when empty.
     */
    private function setDefaultMessage(int $code): string
    {
        return match ($code) {
            Response::HTTP_BAD_REQUEST => 'The request was invalid. The request needs modification',
            Response::HTTP_UNAUTHORIZED => 'Not authorized',
            Response::HTTP_FORBIDDEN => 'Forbidden',
            Response::HTTP_NOT_FOUND => 'Resource not found',
            Response::HTTP_METHOD_NOT_ALLOWED => 'The request method is not supported for the requested resource',
            Response::HTTP_NOT_ACCEPTABLE => 'The requested url is not an acceptable route',
            Response::HTTP_CONFLICT => 'The request could not be processed because of conflict in the current state of the resource',
            Response::HTTP_PRECONDITION_FAILED => 'The server does not meet one of the preconditions that the requester put on the request',
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE => 'The request entity has a media type which the server or resource does not support',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'It was unable to process the contained instructions. The request needs modification',
            Response::HTTP_TOO_MANY_REQUESTS => 'User has sent too many requests in a given amount of time',
            default => 'Internal Server Error',
        };
    }

    /**
     * @param Exception $ex Exception
     */
    private function isPostgresRuntimeException(Exception $ex): bool
    {
        $validCodes = [
            '23505' => 'Unique Violation',
        ];

        return $ex instanceof RuntimeException && $this->isValidErrorCode($validCodes, $ex->getMessage());
    }

    /**
     * @param array<string> $validCodes       Array of excepted codes
     * @param string        $exceptionMessage Exception Message
     */
    private function isValidErrorCode(array $validCodes, string $exceptionMessage): bool
    {
        foreach (array_keys($validCodes) as $code) {
            if (str_contains($exceptionMessage, (string) $code)) {
                return true;
            }
        }

        return false;
    }
}
