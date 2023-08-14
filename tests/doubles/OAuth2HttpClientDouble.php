<?php

declare(strict_types=1);

namespace Tests\doubles;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use kamermans\OAuth2\OAuth2Middleware;

/**
 * Class OAuth2HttpClientDouble.
 */
class OAuth2HttpClientDouble
{
    public const CREDENTIALS = 'client_credentials';
    public const PASSWORD = 'password';
    public const REFRESH_TOKEN = 'refresh_token';

    protected string $accessToken;

    private HandlerStack $stack;

    /**
     * Gets access token.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Sets the token manually.
     */
    public function setAccessToken(OAuth2Middleware $oauth, string $token): void
    {
        $oauth->setAccessToken([
            'access_token' => $token,
        ]);
    }

    /**
     * Sets the Url and config for calling the Auth service.
     *
     * @throws Exception
     */
    public function setAuthConfig(): void
    {
        $config = $this->getServiceConfig('auth');

        $this->authUrl = $config['base_url'].$config['token_url'];
        $this->authConfig = [
            'grant_type' => $config['grant_type'],
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'username' => $config['username'],
            'password' => $config['password'],
        ];
    }

    /**
     * Creates HandlerStack using a MockHandler.
     *
     * @throws Exception
     */
    public function authorize(): void
    {
        $this->setAuthConfig();

        $mockAuthClient = new MockHandler([
            new Response(200),
            new Response(202),
            new RequestException('Error Communicating with Server', new Request('GET', 'test')),
        ]);

        $this->stack = HandlerStack::create($mockAuthClient);
    }

    /**
     * Creates a Mock Guzzle client.
     *
     * @param bool $authorize Sets whether Client requests authorization before making HTTP call
     *
     *@throws Exception
     */
    public function client(bool $authorize = true): Client
    {
        $config = [
            'handler' => $this->stack,
        ];

        if ($authorize) {
            $this->authorize();
            $config['auth'] = 'oauth';
        }

        return new Client($config);
    }

    /**
     * Get config for service.
     *
     * @throws Exception
     */
    protected function getServiceConfig(string $service): array
    {
        $config = config('amhclient.'.$service);

        if (null === $config) {
            throw new Exception('Configuration can not be found.');
        }

        return $config;
    }
}
