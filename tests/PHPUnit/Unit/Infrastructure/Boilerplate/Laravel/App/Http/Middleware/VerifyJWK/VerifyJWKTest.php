<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Laravel\App\Http\Middleware\VerifyJWK;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\ConfigFactories\VerifyJWKConfigArrayFactory;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\VerifyJWK;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

/**
 * To make the tests easier for now they are only working if executed between the years 2000 and 2099!
 *
 * @backupGlobals
 * @backupStaticAttributes
 */
class VerifyJWKTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const Y2000 = 946684800; // Sat Jan 01 2000 00:00:00 GMT+0000
    private const Y2099 = 4070908800; // Thu Jan 01 2099 00:00:00 GMT+0000
    private const KEY_PATH = __DIR__.'/../../../../../../../../../storage/verifyJWK/';

    private VerifyJWK $middleware;
    private TestLogger $logger;
    private VerifyJWKConfigArrayFactory $configFactory;
    private bool $nextCalled = false;

    protected function setUp(): void
    {
        $this->startMockery();
        $this->logger = new TestLogger();
        $this->configFactory = new VerifyJWKConfigArrayFactory();
        $this->middleware = new VerifyJWK($this->logger, $this->configFactory);
    }

    protected function tearDown(): void
    {
        $this->closeMockery();
    }

    /**
     * @return \Generator<mixed>
     */
    public function handleThrowsExceptionDataProvider(): \Generator
    {
        $key = $this->getKeyFileContent('private.pem');
        $defaultToken = $this->createToken($key);
        $defaultConfig = $this->createConfig();

        $genericVerificationFailedMessage = 'VerifyJwk: Token verification failed!';
        $middlewareConfigurationFailedMessage = 'VerifyJwk: Could not set up Middleware configuration!';

        yield 'audience of the token does not exist' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, aud: null),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'issuer of the token does not exist' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, iss: null),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'audience of the token is an empty string' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, aud: ''),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'issuer of the token is an empty string' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, iss: ''),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'audience of the token is not one of the accepted audiences' => [
            '$exceptionClass' => AccessDeniedHttpException::class,
            '$token' => $this->createToken($key, aud: 'https://incorrect.aud/ence'),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'issuer of the token is not one of the accepted issuers' => [
            '$exceptionClass' => AccessDeniedHttpException::class,
            '$token' => $this->createToken($key, iss: 'https://g00gle.com'),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'email_verified-claim does not exist [google-specific $additionalClaims]' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, email_verified: null),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'email_verified-claim is false [google-specific $additionalClaims]' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, email_verified: false),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'email-claim does not exist [google-specific $additionalClaims]' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, email: null),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'email-claim is an empty string [google-specific $additionalClaims]' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, email: ''),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'email-claim does not match the expected value [google-specific $additionalClaims]' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $this->createToken($key, email: ''),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'config is missing the audiences-key' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $defaultToken,
            '$config' => $this->createConfig(audiences: null),
            '$logLevel' => LogLevel::CRITICAL,
            '$logMessage' => $middlewareConfigurationFailedMessage,
        ];

        yield 'config is missing the issuers-key' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $defaultToken,
            '$config' => $this->createConfig(issuers: null),
            '$logLevel' => LogLevel::CRITICAL,
            '$logMessage' => $middlewareConfigurationFailedMessage,
        ];

        yield 'config is missing the jwkPath-key' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $defaultToken,
            '$config' => $this->createConfig(jwkPath: null),
            '$logLevel' => LogLevel::CRITICAL,
            '$logMessage' => $middlewareConfigurationFailedMessage,
        ];

        yield 'config is missing the algorithm-key' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => $defaultToken,
            '$config' => $this->createConfig(algorithms: null),
            '$logLevel' => LogLevel::CRITICAL,
            '$logMessage' => $middlewareConfigurationFailedMessage,
        ];

        yield 'token is expired' => [
            '$exceptionClass' => ExpiredException::class,
            '$token' => $this->createToken($key, exp: self::Y2000),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];

        yield 'token is missing' => [
            '$exceptionClass' => \InvalidArgumentException::class,
            '$token' => null,
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => 'VerifyJwk: No Bearer-Token provided!',
        ];

        yield 'token has invalid signature' => [
            '$exceptionClass' => SignatureInvalidException::class,
            '$token' => $this->createToken($this->getKeyFileContent('invalid.pem')),
            '$config' => $defaultConfig,
            '$logLevel' => LogLevel::WARNING,
            '$logMessage' => $genericVerificationFailedMessage,
        ];
    }

    /**
     * @testdox ->handle($request, $next) calls $next($request) for an authorized $request
     */
    public function testHandleWorks(): void
    {
        $this->configFactory->addConfig('testing', $this->createConfig());
        $route = $this->createRoute();
        $key = $this->getKeyFileContent('private.pem');
        $jwt = $this->createToken($key);
        $request = $this->createRequest($route, $jwt);
        $this->middleware->handle($request, $this->next());

        static::assertTrue($this->nextCalled);
    }

    /**
     * @testdox ->handle($request, $next) logs successful validation to debug
     */
    public function testHandleLogsOnSuccess(): void
    {
        $this->configFactory->addConfig('testing', $this->createConfig());
        $route = $this->createRoute();
        $key = $this->getKeyFileContent('private.pem');
        $jwt = $this->createToken($key);
        $request = $this->createRequest($route, $jwt);
        $this->middleware->handle($request, $this->next());

        static::assertTrue($this->logger->hasDebug('VerifyJwk: Token verified'));
    }

    /**
     * @dataProvider handleThrowsExceptionDataProvider
     * @testdox ->handle($request, $next) throws an $exceptionClass when the $_dataName
     *
     * @param class-string<Throwable>                                                                                                                                              $exceptionClass
     * @param array{jwkPath: string|null, audiences: array<string>|null, issuers: array<string>|null, algorithms: array<string>|null, additionalClaims: array<string, mixed>|null} $config
     */
    public function testHandleThrowsException(
        string $exceptionClass,
        ?string $token,
        array $config,
    ): void {
        $this->expectException($exceptionClass);
        $this->configFactory->addConfig('testing', $config);

        $request = $this->createRequest($this->createRoute(), $token);
        $this->middleware->handle($request, $this->next());
    }

    /**
     * @dataProvider handleThrowsExceptionDataProvider
     * @testdox ->handle($request, $next) logs "$logMessage" [$logLevel] when the $_dataName
     *
     * @param array{jwkPath: string|null, audiences: array<string>|null, issuers: array<string>|null, algorithms: array<string>|null, additionalClaims: array<string, mixed>|null} $config
     */
    public function testHandleLogsOnException(
        string $exceptionClass,
        ?string $token,
        array $config,
        string $logLevel,
        string $logMessage,
    ): void {
        $this->configFactory->addConfig('testing', $config);
        $request = $this->createRequest($this->createRoute(), $token);

        try {
            $this->middleware->handle($request, $this->next());
        } catch (\Throwable) {
            // $this->expectException prevents reliable assertions of something else.
            // so we cannot use it here.
            // but we don't care for the throwable, we just want the logger to get called during a nested catch
            // without letting the test fail
        }

        static::assertTrue($this->logger->hasRecord($logMessage, $logLevel));
    }

    private function next(): \Closure
    {
        return fn () => $this->nextCalled = true;
    }

    /**
     * @param string[]             $audiences
     * @param string[]             $issuers
     * @param string[]             $algorithms
     * @param array<string, mixed> $additionalClaims
     *
     * @return array{jwkPath: string|null, audiences: array<string>|null, issuers: array<string>|null, algorithms: array<string>|null, additionalClaims: array<string, mixed>|null}
     */
    private function createConfig(
        ?string $jwkPath = self::KEY_PATH.'jwk.json',
        ?array $audiences = ['https://api/prototype-cms/v1/testing'],
        ?array $issuers = ['https://accounts.google.com'],
        ?array $algorithms = ['RS256'],
        ?array $additionalClaims = [
            'email' => 'kubernetes-develop@amh-develop-217408.iam.gserviceaccount.com',
            'email_verified' => true,
        ],
    ): array {
        return [
            'jwkPath' => $jwkPath,
            'audiences' => $audiences,
            'issuers' => $issuers,
            'algorithms' => $algorithms,
            'additionalClaims' => $additionalClaims,
        ];
    }

    private function createRoute(?string $tokenProvider = 'testing'): Route
    {
        $route = Mockery::mock(Route::class);
        $route->allows('uri')->withNoArgs()->andReturn('/v1/testing');
        $route->allows('getAction')->with('tokenProvider')->andReturns($tokenProvider);

        return $route;
    }

    private function createRequest(Route $route, ?string $jwt = null): Request
    {
        $request = Mockery::mock(Request::class);
        $request->allows('route')->withNoArgs()->andReturns($route);
        $request->allows('bearerToken')->withNoArgs()->andReturn($jwt);

        return $request;
    }

    private function createToken(
        string $jwk,
        string $kid = 'ZBPr9DWUhPViPHqWuXXTO1ZtjE9taeT3vr3FgE3sGys',
        string $alg = 'RS256',
        ?string $aud = 'https://api/prototype-cms/v1/testing',
        ?int $azp = 1234567890,
        ?string $email = 'kubernetes-develop@amh-develop-217408.iam.gserviceaccount.com',
        ?bool $email_verified = true,
        ?int $exp = self::Y2099,
        ?int $iat = self::Y2000,
        ?string $iss = 'https://accounts.google.com',
        ?int $sub = 1234567890,
    ): string {
        return JWT::encode(
            payload: array_filter([
                'aud' => $aud,
                'azp' => $azp,
                'email' => $email,
                'email_verified' => $email_verified,
                'exp' => $exp,
                'iat' => $iat,
                'iss' => $iss,
                'sub' => $sub,
            ]),
            key: $jwk,
            alg: $alg,
            keyId: $kid
        );
    }

    private function getKeyFileContent(string $key): string
    {
        return file_get_contents(self::KEY_PATH.$key) ?: throw new \Exception('Could not load file '.self::KEY_PATH.$key.' for testing!');
    }
}
