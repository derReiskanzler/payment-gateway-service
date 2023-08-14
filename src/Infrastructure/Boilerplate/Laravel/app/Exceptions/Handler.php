<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, string>
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        // 'password',
        // 'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $e Exception
     *
     * @throws Exception
     */
    public function report(Exception $e): void
    {
        if ($this->shouldReport($e) && app()->bound('sentry')) {
            app('sentry')->withScope(static function (Scope $scope) use ($e): void {
                $scope->setTag('boilerplate_version', config('services.sentry.boilerplate_version'));
                $scope->setTag('framework', 'laravel');
                $scope->setTag('framework_version', app()->version());

                app('sentry')->captureException($e);
            });
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Exception $e): Response|SymfonyResponse
    {
        return (new ExceptionRenderer())->render($request, $e);
    }
}
