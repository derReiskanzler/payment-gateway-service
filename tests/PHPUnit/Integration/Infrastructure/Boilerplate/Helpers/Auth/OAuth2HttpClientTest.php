<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Helpers\Auth;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\OAuth2HttpClient;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\Persistence\FileTokenPersistence;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use ReflectionException;
use Tests\TestCase;

class OAuth2HttpClientTest extends TestCase
{
    private string $baseUri = 'http://testing-auth';

    private string $authUrl = 'http://testing-auth/v1/oauth/token';

    private string $clientCredentials = 'client_credentials';

    private string $passwordCredentials = 'password';

    private string $refreshTokenCredentials = 'refresh_token';

    public function testAuthorizeWithCachePersistence(): void
    {
        $oauthMiddleware = $this->generateOauthMiddleware($this->clientCredentials);
        $token = $oauthMiddleware->getAccessToken();
        static::assertInstanceOf(OAuth2Middleware::class, $oauthMiddleware);
        static::assertNotEmpty($token);

        // call middleware again
        $oauthMiddleware = $this->generateOauthMiddleware($this->clientCredentials);
        $newToken = $oauthMiddleware->getAccessToken();
        static::assertSame($token, $newToken);
    }

    public function testAuthorizeWithMockingFiveMinutesWaiting(): void
    {
        $cachePersistence = $this->generateFileTokenPersistence();

        $oauthMiddleware = $this->generateOauthMiddleware($this->clientCredentials);
        $token = $oauthMiddleware->getAccessToken();
        static::assertInstanceOf(OAuth2Middleware::class, $oauthMiddleware);
        static::assertNotEmpty($token);

        // clear persisted token
        $cachePersistence->deleteToken();

        // call middleware again
        $oauthMiddleware = $this->generateOauthMiddleware($this->clientCredentials);
        $newToken = $oauthMiddleware->getAccessToken();
        static::assertNotSame($token, $newToken);
    }

    public function testAuthorizeWithPasswordGrantType(): void
    {
        $oauthMiddleware = $this->generateOauthMiddleware($this->passwordCredentials);
        $token = $oauthMiddleware->getAccessToken();
        static::assertInstanceOf(OAuth2Middleware::class, $oauthMiddleware);
        static::assertNotEmpty($token);

        // call middleware again
        $oauthMiddleware = $this->generateOauthMiddleware($this->passwordCredentials);
        $newToken = $oauthMiddleware->getAccessToken();
        static::assertSame($token, $newToken);
    }

    public function testAuthorizeWithPasswordGrantTypeMockingFiveMinutes(): void
    {
        $filePersistence = $this->generateFileTokenPersistence();

        $oauthMiddleware = $this->generateOauthMiddleware($this->passwordCredentials);
        $token = $oauthMiddleware->getAccessToken();
        static::assertInstanceOf(OAuth2Middleware::class, $oauthMiddleware);
        static::assertNotEmpty($token);

        // clear persisted token
        $filePersistence->deleteToken();

        // call middleware again
        $oauthMiddleware = $this->generateOauthMiddleware($this->passwordCredentials);
        $newToken = $oauthMiddleware->getAccessToken();
        static::assertNotSame($token, $newToken);
    }

    public function testAuthorizeWithRefreshTokenGrantType(): void
    {
        $oauthMiddleware = $this->generateOauthMiddleware($this->refreshTokenCredentials);
        $token = $oauthMiddleware->getAccessToken();
        static::assertInstanceOf(OAuth2Middleware::class, $oauthMiddleware);
        static::assertNotEmpty($token);

        // call middleware again
        $oauthMiddleware = $this->generateOauthMiddleware($this->refreshTokenCredentials);
        $newToken = $oauthMiddleware->getAccessToken();
        static::assertSame($token, $newToken);
    }

    public function testAuthorizeWithRefreshTokenGrantTypeMockingFiveMinutes(): void
    {
        $cachePersistence = $this->generateFileTokenPersistence();

        $oauthMiddleware = $this->generateOauthMiddleware($this->refreshTokenCredentials);
        $token = $oauthMiddleware->getAccessToken();
        static::assertInstanceOf(OAuth2Middleware::class, $oauthMiddleware);
        static::assertNotEmpty($token);

        // clear persisted token
        $cachePersistence->deleteToken();

        // call middleware again
        $oauthMiddleware = $this->generateOauthMiddleware($this->refreshTokenCredentials);
        $newToken = $oauthMiddleware->getAccessToken();
        static::assertNotSame($token, $newToken);
    }

    public function testAuthorizeWithInvalidGrantType(): void
    {
        $this->expectException(Exception::class);
        $this->generateOauthMiddleware('any');
    }

    public function testFileTokenPersistance(): void
    {
        $filePersistence = $this->generateFileTokenPersistence();

        static::assertInstanceOf(FileTokenPersistence::class, $filePersistence);
    }

    public function testAuthClient(): void
    {
        $oauthClient = new OAuth2HttpClient();
        $authClient = $this->callMethod($oauthClient, 'authClient', [$this->authUrl]);

        static::assertInstanceOf(Client::class, $authClient);
    }

    public function testCreateGuzzleClientWithAuthorization(): void
    {
        $oauthClient = new OAuth2HttpClient();
        $guzzleClient = $oauthClient->client();

        static::assertSame('oauth', $guzzleClient->getConfig('auth'));
    }

    public function testGuzzleStack(): void
    {
        $oauthClient = new OAuth2HttpClient();
        $guzzleStack = $this->callMethod($oauthClient, 'guzzleStack', [$this->generateOauthMiddleware($this->clientCredentials)]);

        static::assertInstanceOf(HandlerStack::class, $guzzleStack);
    }

    /**
     * @param string $grantType grant type
     *
     * @throws ReflectionException
     */
    private function generateOauthMiddleware(string $grantType): OAuth2Middleware
    {
        $oauthClient = new OAuth2HttpClient();

        $mockedRequestBody = '{"token_type": "Bearer", "expires_in" : "300", "access_token" : "'.random_int(1000, 100000).'"}';
        $client = new Client(['base_uri' => $this->baseUri]);

        /**
         * @var MockInterface&Client $clientMock
         */
        $clientMock = Mockery::mock($client);
        $guzzleClient = $clientMock
            ->makePartial()
            ->allows('send')
            ->andReturns(new Response(200, [], $mockedRequestBody))
            ->getMock();

        return $this->callMethod($oauthClient, 'getAuthMiddleware', [
            $guzzleClient,
            $grantType,
            [
                'client_id' => 13,
                'client_secret' => 'any',
                'grant_type' => $grantType,
                'username' => '',
                'password' => '',
            ],
            $this->storagePath(),
        ]);
    }

    /**
     * @throws ReflectionException
     *
     * @return mixed
     */
    private function generateFileTokenPersistence()
    {
        $oauthClient = new OAuth2HttpClient();

        return $this->callMethod($oauthClient, 'fileTokenPersistence', [$this->storagePath()]);
    }

    private function storagePath(): string
    {
        return sys_get_temp_dir().\DIRECTORY_SEPARATOR.env('APP_AMH_OAUTH_PERSISTENCE_FILE_NAME');
    }

    /**
     * @param array<mixed> $args
     *
     * @throws ReflectionException
     */
    private function callMethod(object $obj, string $name, array $args = []): mixed
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
