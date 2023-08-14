<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions\Response;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use JsonException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ErrorResponse implements ErrorResponseInterface
{
    protected int $code;

    protected string $message;

    /**
     * @var array<ValidationError>
     */
    protected array $errors = [];

    protected Serializer $serializer;

    public function __construct(string $message, int $errorCode)
    {
        $this->code = $errorCode;
        $this->message = $message;
    }

    /**
     * Gets response.
     *
     * @throws BindingResolutionException
     * @throws JsonException
     */
    public function getResponse(): Response
    {
        $response = response($this->responseStructure(), $this->code);

        return $response instanceof Response ? $response : $response->make();
    }

    /**
     * @throws JsonException
     *
     * @return array<string, int|string|ValidationError>>
     */
    private function responseStructure(): array
    {
        return array_merge($this->bindRequiredKey(), $this->bindOptionalErrorsKeyToStructure());
    }

    /**
     * @return array<string, int|string>
     */
    private function bindRequiredKey(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
        ];
    }

    /**
     * @throws JsonException
     *
     * @return array<string, ValidationError>
     */
    private function bindOptionalErrorsKeyToStructure(): array
    {
        if (\count($this->errors)) {
            return [
                'errors' => json_decode(
                    json: $this->getSerializer()->serialize($this->errors, 'json'),
                    associative: true,
                    flags: \JSON_THROW_ON_ERROR
                ),
            ];
        }

        return [];
    }

    /**
     * Gets serializer.
     */
    private function getSerializer(): Serializer
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(), new ArrayDenormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        return $this->serializer;
    }
}
