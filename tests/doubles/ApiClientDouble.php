<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces\CrudApiClientInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces\RestApiClientInterface;
use Exception;
use Psr\Http\Message\ResponseInterface;

class ApiClientDouble implements CrudApiClientInterface, RestApiClientInterface
{
    public bool $withAuth = true;
    protected OAuth2HttpClientDouble $amhClientDouble;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->beforeConstruct();

        $this->amhClientDouble = new OAuth2HttpClientDouble();
    }

    /**
     * Executes before constructor logic.
     */
    public function beforeConstruct(): void
    {
    }

    /**
     * Find all items.
     *
     * @throws Exception
     */
    public function findAll(string $url): ResponseInterface|array
    {
        return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->get($url));
    }

    /**
     * Find item by id.
     *
     * @throws Exception
     */
    public function findById(string $url, $id): ResponseInterface|array
    {
        return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->get(sprintf('%s/%s', $url, $id)));
    }

    /**
     * Create new item.
     *
     * @throws Exception
     */
    public function create(string $url, array $data): ResponseInterface|array
    {
        return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->post($url, $data));
    }

    /**
     * Patch update item by id.
     *
     * @throws Exception
     */
    public function update(string $url, string|int $id, array $data): ResponseInterface|array
    {
        return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->patch(sprintf('%s/%s', $url, $id), $data));
    }

    /**
     * Replace update item by id.
     *
     * @throws Exception
     */
    public function replace(string $url, string|int $id, array $data): ResponseInterface|array
    {
        return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->put(sprintf('%s/%s', $url, $id), $data));
    }

    /**
     * Delete item by id.
     *
     * @throws Exception
     */
    public function deleteById(string $url, string|int $id): ResponseInterface|array
    {
        return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->delete(sprintf('%s/%s', $url, $id)));
    }

    /**
     * GET method.
     *
     * @param bool $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     */
    public function get(string $url, array $params, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->get($url, $params));
        }

        return $this->amhClientDouble->client($this->withAuth)->get($url, $params);
    }

    /**
     * POST method.
     *
     * @param bool $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     */
    public function post(string $url, array $params, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->post($url, $params));
        }

        return $this->amhClientDouble->client($this->withAuth)->post($url, $params);
    }

    /**
     * PUT method.
     *
     * @param bool $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     */
    public function put(string $url, array $params, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->put($url, $params));
        }

        return $this->amhClientDouble->client($this->withAuth)->put($url, $params);
    }

    /**
     * PATCH method.
     *
     * @param bool $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     */
    public function patch(string $url, array $params, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->patch($url, $params));
        }

        return $this->amhClientDouble->client($this->withAuth)->patch($url, $params);
    }

    /**
     * DELETE method.
     *
     * @param bool $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     */
    public function delete(string $url, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->responseToJson($this->amhClientDouble->client($this->withAuth)->delete($url));
        }

        return $this->amhClientDouble->client($this->withAuth)->delete($url);
    }

    /**
     * Parses HTTP response to Json Object.
     *
     * @param ResponseInterface $response Response Object
     *
     * @return mixed
     */
    private function responseToJson(ResponseInterface $response)
    {
        return json_decode(
            json: $response->getBody()->getContents(),
            associative: true,
            flags: \JSON_THROW_ON_ERROR
        );
    }
}
