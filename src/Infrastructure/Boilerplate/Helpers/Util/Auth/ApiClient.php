<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces\CrudApiClientInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces\RestApiClientInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Traits\GetServiceConfigTrait;
use Allmyhomes\MailRendererClient\Contracts\AmhClientInterface;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiClient.
 */
class ApiClient implements CrudApiClientInterface, RestApiClientInterface, AmhClientInterface
{
    use GetServiceConfigTrait;

    /**
     * @var array<string>
     */
    public array $locationHeader = [];

    public bool $withAuth = true;

    public function __construct(private OAuth2HttpClient $amhClient)
    {
        $this->beforeConstruct();
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
     *
     * @return array<string, mixed>
     */
    public function findAll(string $url): array
    {
        return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->get($url));
    }

    /**
     * Find item by id.
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function findById(string $url, string|int $id): array
    {
        return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->get(sprintf('%s/%s', $url, $id)));
    }

    /**
     * Create new item.
     *
     * @param array<string, mixed> $data
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function create(string $url, array $data): array
    {
        $response = $this->amhClient->client($this->withAuth)->post($url, $data);
        $this->setLocationHeader($response);

        return $this->decodeJsonResponse($response);
    }

    /**
     * Patch update item by id.
     *
     * @param array<string, mixed> $data
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function update(string $url, string|int $id, array $data): array
    {
        return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->patch(sprintf('%s/%s', $url, $id), $data));
    }

    /**
     * Replace update item by id.
     *
     * @param array<string, mixed> $data
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function replace(string $url, string|int $id, array $data): array
    {
        return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->put(sprintf('%s/%s', $url, $id), $data));
    }

    /**
     * Delete item by id.
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function deleteById(string $url, string|int $id): array
    {
        return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->delete(sprintf('%s/%s', $url, $id)));
    }

    /**
     * GET method.
     *
     * @param array<string, mixed> $params
     * @param bool                 $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     *
     * @return ResponseInterface|array<mixed>
     */
    public function get(string $url, array $params, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->get($url, $params));
        }

        return $this->amhClient->client($this->withAuth)->get($url, $params);
    }

    /**
     * POST method.
     *
     * @param array<string, mixed> $params
     * @param bool                 $toJson decode response from json to assoc. array?
     *
     * @throws Exception
     *
     * @return ResponseInterface|array<mixed>
     */
    public function post(string $url, array $params, $toJson = true): ResponseInterface|array
    {
        if ($toJson) {
            return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->post($url, $params));
        }

        $response = $this->amhClient->client($this->withAuth)->post($url, $params);
        $this->setLocationHeader($response);

        return $response;
    }

    /**
     * PUT method.
     *
     * @param array<string, mixed> $params
     * @param bool                 $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     *
     * @return ResponseInterface|array<mixed>
     */
    public function put(string $url, array $params, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->put($url, $params));
        }

        return $this->amhClient->client($this->withAuth)->put($url, $params);
    }

    /**
     * PATCH method.
     *
     * @param array<string, mixed> $params
     * @param bool                 $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     *
     * @return ResponseInterface|array<mixed>
     */
    public function patch(string $url, array $params, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->patch($url, $params));
        }

        return $this->amhClient->client($this->withAuth)->patch($url, $params);
    }

    /**
     * DELETE method.
     *
     * @param bool $decodeJson decode response from json to assoc. array?
     *
     * @throws Exception
     *
     * @return ResponseInterface|array<mixed>
     */
    public function delete(string $url, bool $decodeJson = true): ResponseInterface|array
    {
        if ($decodeJson) {
            return $this->decodeJsonResponse($this->amhClient->client($this->withAuth)->delete($url));
        }

        return $this->amhClient->client($this->withAuth)->delete($url);
    }

    /**
     * Sets Location Header.
     */
    public function setLocationHeader(ResponseInterface $response): void
    {
        $this->locationHeader = $response->getHeader('Location');
    }

    /**
     * Gets Location Header.
     *
     * @return array<string, mixed>
     */
    public function getLocationHeader(): array
    {
        return $this->locationHeader;
    }

    /**
     * Parses HTTP response to Json Object.
     *
     * @throws JsonException if the response is no valid json
     *
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response): array
    {
        return json_decode(
            json: $response->getBody()->getContents(),
            associative: true,
            flags: \JSON_THROW_ON_ERROR
        );
    }
}
