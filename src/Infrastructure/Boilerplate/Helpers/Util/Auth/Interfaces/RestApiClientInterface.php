<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface RestApiInterface.
 */
interface RestApiClientInterface extends ApiClientInterface
{
    /**
     * Get method.
     *
     * @param array<string, mixed> $params
     * @param bool                 $decodeJson decode response from json to assoc. array?
     *
     * @return ResponseInterface|array<mixed>
     */
    public function get(string $url, array $params, bool $decodeJson = true): ResponseInterface|array;

    /**
     * Post method.
     *
     * @param array<string, mixed> $params<string>
     * @param bool                 $decodeJson     decode response from json to assoc. array?
     *
     * @return ResponseInterface|array<mixed>
     */
    public function post(string $url, array $params, bool $decodeJson = true): ResponseInterface|array;

    /**
     * Put method.
     *
     * @param array<string, mixed> $params
     * @param bool                 $decodeJson decode response from json to assoc. array?
     *
     * @return ResponseInterface|array<mixed>
     */
    public function put(string $url, array $params, bool $decodeJson = true): ResponseInterface|array;

    /**
     * Patch method.
     *
     * @param array<string, mixed> $params
     * @param bool                 $decodeJson decode response from json to assoc. array?
     *
     * @return ResponseInterface|array<mixed>
     */
    public function patch(string $url, array $params, bool $decodeJson = true): ResponseInterface|array;

    /**
     * Delete method.
     *
     * @param bool $decodeJson decode response from json to assoc. array?
     *
     * @return ResponseInterface|array<mixed>
     */
    public function delete(string $url, bool $decodeJson = true): ResponseInterface|array;
}
