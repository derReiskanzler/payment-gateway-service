<?php

/**
 * @noinspection PhpUnhandledExceptionInspection PhpDocMissingThrowsInspection PhpMultipleClassDeclarationsInspection
 * PhpStorm says 'Missing "@throw Throwable"' in almost every class... Seriously? I think there are enough annotations for every case...
 */
declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\ConfigFactories\AbstractVerifyJWKConfigFactory;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Verifies JW-Tokens (JWT) against a JW-Keyset (JWK).
 */
class VerifyJWK
{
    /**
     * Configuration for the tokenProvider associated with the endpoint.
     */
    private VerifyJWKConfig $config;

    /**
     * (Wrapper around the) Incoming Request.
     */
    private VerifyJWKRequest $request;

    /**
     * Keyset to validate the tokens against
     * (if successfully loaded and decoded).
     *
     * @var ?array{iss: string, sub: string, aud: string, iat: int, exp: int}
     */
    private ?array $jwk = null;

    /**
     * Decoded Token
     * (if successfully verified and decoded).
     *
     * @var ?array{aud: string, azp: string, email: string, email_verified:bool, exp:int, iat:int, iss:string, sub:string}
     */
    private ?array $token = null;

    public function __construct(
        private LoggerInterface $logger,
        private AbstractVerifyJWKConfigFactory $configFactory,
    ) {
    }

    /**
     * Verifies the JWT-Token of the passed request.
     *
     * @throws \RuntimeException         if the jwkPath is not accessible
     * @throws \JsonException            if the file stored at the jwkPath is not valid json
     * @throws \InvalidArgumentException if the provided JWK set is empty
     * @throws \UnexpectedValueException if the provided JWK set is invalid
     * @throws \DomainException          if an OpenSSL failure occurs (documented in JWK::parseKeySet, don't know how it can happen)
     * @throws \InvalidArgumentException if the key is empty
     * @throws \InvalidArgumentException if the token does not have an aud-claim
     * @throws \InvalidArgumentException if the token does not have an iss-claim
     * @throws \InvalidArgumentException if the token does not have all additional claims
     * @throws \UnexpectedValueException if there is a wrong number of segments
     * @throws \UnexpectedValueException if the header encoding is invalid
     * @throws \UnexpectedValueException if the claims encoding is invalid
     * @throws \UnexpectedValueException if the signature encoding is invalid
     * @throws \UnexpectedValueException if the token contains no algorithm
     * @throws \UnexpectedValueException if the token uses an unsupported algorithm
     * @throws \UnexpectedValueException if the algorithm of the token is not whitelisted
     * @throws \UnexpectedValueException if the key is incorrect for the chosen algorithm
     * @throws AccessDeniedHttpException if the verification of the signature failed
     * @throws AccessDeniedHttpException if the token is not valid yet (used before 'nbf' and 'iat')
     * @throws AccessDeniedHttpException if the token is expired
     * @throws AccessDeniedHttpException if the token is not intended for this audience
     * @throws AccessDeniedHttpException if the issuer of the token is not accepted
     * @throws AccessDeniedHttpException if one of the additionally configured claims of the token does not match the expected value
     */
    final public function handle(Request $request, \Closure $next): mixed
    {
        $this->setUpMiddleware($request); // needs special handling for the logger
        try {
            $this->log(LogLevel::DEBUG, 'Initiating Token verification'); // w/o setUpMiddleware() it would log nothing useful for product-teams, therefore it's the 2nd step

            $this->loadJWK();
            $this->decodeAndVerifyToken();
            $this->checkClaimIsOneOf('iss', $this->config->getIssuers(), 'Tokens from this issuer are not accepted!');
            $this->checkClaimIsOneOf('aud', $this->config->getAudiences(), 'Token not intended for this audience');
            $this->checkAdditionalClaims();

            $this->log(LogLevel::DEBUG, 'Token verified');
        } catch (\Throwable $throwable) {
            $this->log(LogLevel::WARNING, 'Token verification failed!', throwable: $throwable);
            throw $throwable;
        }

        return $next($request);
    }

    /**
     * Sets up the middleware with static information (config & unaltered data from request).
     *
     * @throws \InvalidArgumentException if the bearer token is missing
     * @throws \InvalidArgumentException if the config is missing a required key
     */
    private function setUpMiddleware(Request $request): void
    {
        try {
            $this->request = new VerifyJWKRequest($request);
        } catch (\Throwable $throwable) {
            match ($throwable->getMessage()) {
                'No Bearer-Token provided!' => $this->log(
                    LogLevel::WARNING,
                    'No Bearer-Token provided!',
                    hint: 'Check if the client that made the call has included a bearer token',
                    throwable: $throwable,
                    skippedSetup: true,
                ),
                'Missing TokenProvider-Config for this route!' => $this->log(
                    LogLevel::WARNING,
                    'Missing TokenProvider-Config for this route!',
                    hint: 'Check if the route has a "tokenProvider"-key associated with this middleware',
                    throwable: $throwable,
                    skippedSetup: true,
                ),
                default => $this->log(
                    LogLevel::WARNING,
                    'Could not set up Middleware for the request',
                    hint: 'Setup failed for unknown reason. The token is there and a tokenProvider is configured for the route. Happy debugging!',
                    throwable: $throwable,
                    skippedSetup: true,
                ),
            };
            throw $throwable;
        }

        try {
            $this->config = $this->configFactory->createConfigForTokenProvider($this->request->getTokenProvider());
        } catch (\Throwable $throwable) {
            $this->log(
                LogLevel::CRITICAL,
                'Could not set up Middleware configuration!',
                hint: 'Check if your configuration exists and is complete and ensure a "tokenProvider" is associated in this routes definition!',
                throwable: $throwable,
                skippedSetup: true,
            );
            throw $throwable;
        }
    }

    /**
     * Loads the JWK from the configured jwkPath.
     *
     * @throws \RuntimeException if the configured jwkPath did not resolve to a readable file
     * @throws \JsonException    if the file at jwkPath is not valid json
     */
    private function loadJWK(): void
    {
        $this->log(LogLevel::DEBUG, 'Loading JWKs for Token verification');
        try {
            $path = $this->config->getJwkPath();
            $jwk = file_get_contents($path) ?: throw new \RuntimeException('Could not find '.$path);
            $this->jwk = json_decode($jwk, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Throwable $throwable) {
            $this->log(
                level: LogLevel::CRITICAL,
                message: 'Could not load JWKs!',
                hint: 'Check your verify-jwk-configuration for the associated "tokenProvider" and if the jwkPath is accessible (locally or remote) from your application and contains a json',
                throwable: $throwable
            );
            throw $throwable;
        }
    }

    /**
     * Decodes the token and verifies basic integrity, e.g. valid encodings, correct signature, valid timestamps etc.
     * In the process the token is also authenticated against the stored public key.
     *
     * @throws \InvalidArgumentException if the provided JWK set is empty
     * @throws \UnexpectedValueException if the provided JWK set is invalid
     * @throws \DomainException          on an OpenSSL failure (documented in JWK::parseKeySet, don't know how it can happen)
     * @throws \InvalidArgumentException if the key is empty
     * @throws \UnexpectedValueException if there is a wrong number of segments
     * @throws \UnexpectedValueException if the header encoding is invalid
     * @throws \UnexpectedValueException if the claims encoding is invalid
     * @throws \UnexpectedValueException if the signature encoding is invalid
     * @throws \UnexpectedValueException if the token contains no algorithm
     * @throws \UnexpectedValueException if the token uses an unsupported algorithm
     * @throws \UnexpectedValueException if the algorithm of the token is not whitelisted
     * @throws \UnexpectedValueException if the key is incorrect for the chosen algorithm
     * @throws SignatureInvalidException if the verification of the signature failed
     * @throws BeforeValidException      if the token is not valid yet (used before 'nbf' and 'iat')
     * @throws ExpiredException          if the token is expired
     */
    private function decodeAndVerifyToken(): void
    {
        /**
         * @var array{aud: string, azp: string, email: string, email_verified:bool, exp:int, iat:int, iss:string, sub:string} $token
         */
        $token = (array) JWT::decode(
            $this->request->getToken(),
            JWK::parseKeySet((array) $this->jwk),
            $this->config->getAlgorithms(),
        );

        $this->token = $token;
    }

    /**
     * Checks if the value of a claim is on of the expected values ($oneOf).
     *
     * @param string[] $oneOf
     *
     * @throws \InvalidArgumentException if the token does not have the expected claim
     * @throws AccessDeniedHttpException if the claim does not match any of the expected values
     */
    private function checkClaimIsOneOf(string $claim, array $oneOf, string $mismatchMessage): void
    {
        $value = $this->token[$claim] ?? throw new \InvalidArgumentException('No '.$claim.'-claim provided');

        \in_array($value, $oneOf, true) ?: throw new AccessDeniedHttpException($mismatchMessage);
    }

    /**
     * Checks if the token contains all configured additional claims. They must match 1-1.
     *
     * @throws \InvalidArgumentException if the token does not have all additional claims
     * @throws AccessDeniedHttpException if one of the claims does not match the expected value
     */
    private function checkAdditionalClaims(): void
    {
        foreach ($this->config->getAdditionalClaims() as $claim => $value) {
            $this->checkClaimIsOneOf($claim, [$value], 'The '.$claim.'-claim is not matching the expected value!');
        }
    }

    /**
     * Logging helper to ensure consistent logs.
     * Data that is not set yet is filtered out.
     */
    private function log(
        string $level,
        string $message,
        ?string $hint = null,
        ?\Throwable $throwable = null,
        ?bool $skippedSetup = false,
    ): void {
        $this->logger->log(
            $level,
            'VerifyJwk: '.$message,
            [
            'verifyJWK' => array_filter([
                'hint' => $hint,
                'request' => $skippedSetup ? null : $this->request->toArray(),
                'config' => $skippedSetup ? null : $this->config->toArray(),
                'jwk' => $this->jwk,
                'token' => $this->token,
                'throwable' => $throwable ? [
                    'message' => $throwable->getMessage(),
                    'location' => $throwable->getFile().':'.$throwable->getLine(),
                    'trace' => $throwable->getTraceAsString(),
                ] : null,
            ]),
        ]
        );
    }
}
