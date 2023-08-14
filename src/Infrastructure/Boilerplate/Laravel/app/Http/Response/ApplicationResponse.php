<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApplicationResponse.
 */
class ApplicationResponse implements ResponseInterface
{
    private Manager $fractalManager;

    public function __construct()
    {
        $this->fractalManager = ManagerFactory::initialize();
    }

    /**
     * @param LengthAwarePaginator $data        the repository response
     * @param TransformerAbstract  $transformer transformer
     */
    public function paginator(LengthAwarePaginator $data, TransformerAbstract $transformer): JsonResponse
    {
        $resource = new Collection($data, $transformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($data));

        return new JsonResponse($this->fractalManager->createData($resource)->toArray(), Response::HTTP_OK);
    }

    public function collection(mixed $collection, mixed $transformer): JsonResponse
    {
        $resource = new Collection($collection, $transformer);

        return new JsonResponse($this->fractalManager->createData($resource)->toArray(), Response::HTTP_OK);
    }

    /**
     * @param mixed $item model item to be transformed to json response
     */
    public function item(mixed $item, TransformerAbstract $transformer): JsonResponse
    {
        $resource = new Item($item, $transformer);

        return new JsonResponse($this->fractalManager->createData($resource)->toArray(), Response::HTTP_OK);
    }

    /**
     * @param string|null $location location url
     * @param string|null $content  response content
     */
    public function created(?string $location = null, ?string $content = null): JsonResponse
    {
        $headers = [];
        if (null !== $location) {
            $headers = ['Location' => $location];
        }

        return new JsonResponse($content, Response::HTTP_CREATED, $headers);
    }

    public function noContent(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
