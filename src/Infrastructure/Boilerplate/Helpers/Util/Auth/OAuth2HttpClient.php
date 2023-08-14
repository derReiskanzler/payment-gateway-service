<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Traits\GetServiceConfigTrait;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\GrantType\PasswordCredentials;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\Persistence\FileTokenPersistence;
use kamermans\OAuth2\Signer\ClientCredentials\PostFormData;

/**
 * Class OAuth2HttpClient.
 */
class OAuth2HttpClient
{
    use GetServiceConfigTrait;

    private const CREDENTIALS = 'client_credentials';
    private const PASSWORD = 'password';
    private const REFRESH_TOKEN = 'refresh_token';

    private const PERSISTENCE_FILE_NAME = 'guzzle-auth-token-persistence.cache';

    protected string $token;

    private string $authUrl = '';

    /**
     * @var array<string, mixed>
     */
    private array $authConfig = [];

    private HandlerStack $stack;

    /**
     * Gets access token.
     */
    public function getAccessToken(): string
    {
        return $this->token;
    }

    /**
     * Sets the token manually.
     */
    public function setToken(OAuth2Middleware $oauth, string $token): void
    {
        $oauth->setAccessToken([
            'access_token' => $token,
        ]);
    }

    /**
     * Sets the Url and credentials for calling the AllMyHomes Auth Service.
     *
     * @throws Exception if config amhclient.amh_oauth_credentials.auth.base_url does not exist
     */
    public function setAuthConfig(): void
    {
        $authBaseUri = $this->getServiceConfig('auth.base_url');

        $this->authUrl = $authBaseUri.'/v1/oauth/token';
        $this->authConfig = [
            'client_id' => $this->getAuthCredential('client_id'),
            'client_secret' => $this->getAuthCredential('client_secret'),
            'grant_type' => $this->getAuthCredential('grant_type'),
            'username' => $this->getAuthCredential('username'),
            'password' => $this->getAuthCredential('password'),
        ];
    }

    /**
     * Retrieves the Access Token from OAuth server
     * Creates HandlerStack by default for authorized Guzzle Client.
     *
     * @throws Exception
     */
    public function authorize(): void
    {
        $this->setAuthConfig();

        $authClient = $this->authClient($this->authUrl);

        $oauth = $this->getAuthMiddleware($authClient, $this->authConfig['grant_type'], $this->authConfig, $this->storagePath());

        $this->guzzleStack($oauth);
    }

    /**
     * Requests an Access Token from the Auth service.
     *
     * @throws Exception
     */
    public function requestAccessToken(): string
    {
        $this->setAuthConfig();

        $authClient = new Client([
            'base_uri' => $this->authUrl,
            'form_params' => $this->authConfig,
            'headers' => ['content-type' => 'multipart/form-data'],
        ]);

        return json_decode(
            json: $authClient->post($this->authUrl)->getBody()->getContents(),
            associative: true,
            flags: \JSON_THROW_ON_ERROR
        )['access_token'];
    }

    /**
     * Creates a Guzzle client.
     *
     * @param bool $authorize Sets whether Client requests authorization before making HTTP call
     *
     * @throws Exception
     */
    public function client(bool $authorize = true): Client
    {
        $config = [];

        if ($authorize) {
            $this->authorize();
            $config = [
                'handler' => $this->stack,
                'auth' => 'oauth',
            ];
        }

        return new Client($config);
    }

    /**
     * Returns AllMyHomes OAuth Service credentials.
     *
     * @throws Exception if config amhclient.amh_oauth_credentials.$configKey does not exist
     */
    private function getAuthCredential(string $configKey): string|int
    {
        $config = config('amhclient.amh_oauth_credentials.'.$configKey);

        if (null === $config) {
            throw new Exception(sprintf('Missing config value for "amhclient.amh_oauth_credentials.%s"', $configKey));
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $authConfig
     *
     * @throws Exception if $configuredGrantType is not supported
     */
    private function getAuthMiddleware(Client $authClient, string $configuredGrantType, array $authConfig, string $storagePath): OAuth2Middleware
    {
        switch ($configuredGrantType) {
            case self::CREDENTIALS:
                $grantType = new ClientCredentials($authClient, $authConfig);
                $clientCredentialsSigner = new PostFormData();
                $oauth = new OAuth2Middleware($grantType, null, $clientCredentialsSigner);
                break;
            case self::PASSWORD:
                $grantType = new PasswordCredentials($authClient, $authConfig);
                $oauth = new OAuth2Middleware($grantType);
                break;
            case self::REFRESH_TOKEN:
                $grantType = new ClientCredentials($authClient, $authConfig);
                $refreshGrantType = new RefreshToken($authClient, $authConfig);
                $oauth = new OAuth2Middleware($grantType, $refreshGrantType);
                break;
            default:
                throw new Exception(sprintf('Grant Type "%s" is not supported.', $configuredGrantType));
        }

        $oauth->setTokenPersistence($this->fileTokenPersistence($storagePath));

        return $oauth;
    }

    private function fileTokenPersistence(string $filePath): FileTokenPersistence
    {
        return new FileTokenPersistence($filePath);
    }

    private function storagePath(): string
    {
        return sys_get_temp_dir().\DIRECTORY_SEPARATOR.config('auth.oauth_persistence_file_name', self::PERSISTENCE_FILE_NAME);
    }

    private function authClient(string $authUrl): Client
    {
        return new Client(['base_uri' => $authUrl]);
    }

    private function guzzleStack(OAuth2Middleware $oauth): HandlerStack
    {
        $this->stack = HandlerStack::create();
        $this->stack->push($oauth);

        return $this->stack;
    }
}
