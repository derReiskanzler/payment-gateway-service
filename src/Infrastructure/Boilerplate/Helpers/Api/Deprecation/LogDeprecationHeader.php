<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation;

use Allmyhomes\TokenVerification\Services\TokenService;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class LogDeprecationHeader implements ResponseDeprecationInterface
{
    public function __construct(
        private ResponseDeprecationInterface $responseDeprecation,
        private Request $request,
        private LoggerInterface $logger
    ) {
    }

    public function deprecate(Response $response): Response
    {
        $response = $this->responseDeprecation->deprecate($response);

        $this->log();

        return $response;
    }

    private function log(): void
    {
        $payload = $this->requesterInfo();

        $this->logger->warning('Deprecated Route used', [
            'deprecation' => [
                'method' => $this->request->getMethod(),
                'pathInfo' => $this->request->getPathInfo(),
                'uri' => $this->request->getUri(),
                'fullUrl' => $this->request->fullUrl(),
                'userInfo' => $this->request->getUserInfo(),
                'requested_service_id' => $payload['aud'] ?? '',
                'jwtUser' => $payload['user'] ?? [],
            ],
        ]);
    }

    /**
     * @return array<string>
     */
    private function requesterInfo(): array
    {
        return $this->request->get(TokenService::JWT_PAYLOAD_INDEX) ?? [];
    }
}
