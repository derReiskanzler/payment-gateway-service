<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces;

use Exception;
use Psr\Http\Message\ResponseInterface;

interface CrudApiClientInterface extends ApiClientInterface
{
    /**
     * Find all.
     *
     * @return ResponseInterface|array<mixed>
     */
    public function findAll(string $url): ResponseInterface|array;

    /**
     * Find item by ID.
     *
     * @return ResponseInterface|array<mixed>
     */
    public function findById(string $url, string|int $id): ResponseInterface|array;

    /**
     * Create new item.
     *
     * @param array<string, mixed> $data
     *
     * @throws Exception
     *
     * @return ResponseInterface|array<mixed>
     */
    public function create(string $url, array $data): ResponseInterface|array;

    /**
     * Patch update item by id.
     *
     * @param array<string, mixed> $data
     *
     * @return ResponseInterface|array<mixed>
     */
    public function update(string $url, string|int $id, array $data): ResponseInterface|array;

    /**
     * Replace item by id (Put).
     *
     * @param array<string, mixed> $data
     *
     * @return ResponseInterface|array<mixed>
     */
    public function replace(string $url, string|int $id, array $data): ResponseInterface|array;

    /**
     * Delete item by id.
     *
     * @return ResponseInterface|array<mixed>
     */
    public function deleteById(string $url, string|int $id): ResponseInterface|array;
}
