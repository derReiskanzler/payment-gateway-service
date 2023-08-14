<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers;

use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\LogManager;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractHealthzController.
 *
 * @codeCoverageIgnore
 */
abstract class AbstractHealthzController extends Controller
{
    /**
     * @var array<string>
     */
    private array $errors = [];

    /**
     * @var array<string>
     */
    private array $warnings = [];

    /**
     * Checks multiple aspects of the application to ensure proper functionality.
     */
    abstract protected function check(): void;

    /**
     * The function that is called by the router.
     */
    final public function healthz(): JsonResponse
    {
        $this->check();

        return $this->response();
    }

    /**
     * Checks if a config value is empty.
     */
    protected function configValueEmpty(string $configPath): bool
    {
        return empty(config($configPath));
    }

    /**
     * Checks if a file exist and is not empty.
     *
     * @param string $path   path to the file (or to the config value containing the path)
     * @param bool   $config if true, $path will be used as index in the config to get the real path
     */
    protected function fileExistAndNotEmpty(string $path, bool $config = false): bool
    {
        if ($config) {
            $path = (string) config($path);
        }

        return file_exists($path) && filesize($path) > 0;
    }

    /**
     * Checks if a connection to the database can be established.
     */
    protected function databaseConnectionWorks(): bool
    {
        try {
            DB::connection()->getPdo();
        } catch (Exception) {
            return false;
        }

        return true;
    }

    /**
     * Adds a warning to the response.
     * Warnings won't fail the health-check!
     */
    protected function warning(string $message): void
    {
        $this->warnings[] = $message;
    }

    /**
     * Adds an error to the response.
     * Errors will fail the health-check!
     */
    protected function error(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * Renders the response for the healthz-check.
     * If any error occurred an errorcode will be used (500).
     */
    private function response(): JsonResponse
    {
        $responseFactory = app(ResponseFactory::class);
        if ($this->errors) {
            $this->logErrors();

            return $responseFactory->json($this->errors)->setStatusCode(500);
        }

        return $responseFactory->json(['OK'])->setStatusCode(200);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function logErrors(): void
    {
        /** @var LogManager $logger */
        $logger = app()->get('log');
        $context = [
            'errors' => $this->errors,
            'warnings' => $this->warnings,
        ];
        $logger->error('Health check failed', $context);
    }
}
