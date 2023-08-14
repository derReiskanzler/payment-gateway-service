<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\TransformerAbstract;

/**
 * @template T
 */
interface ResponseInterface
{
    /**
     * @param LengthAwarePaginator<T> $data
     */
    public function paginator(LengthAwarePaginator $data, TransformerAbstract $transformer): JsonResponse;

    /**
     * @param T $item
     */
    public function item(mixed $item, TransformerAbstract $transformer): JsonResponse;

    /**
     * @param string|null $location can be null
     * @param string|null $content  can be null
     */
    public function created(?string $location = null, ?string $content = null): JsonResponse;

    public function noContent(): JsonResponse;
}
