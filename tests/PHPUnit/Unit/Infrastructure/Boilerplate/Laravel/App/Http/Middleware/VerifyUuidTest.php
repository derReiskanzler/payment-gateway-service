<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Laravel\App\Http\Middleware;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyUuid;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VerifyUuidTest extends TestCase
{
    private const USER_ID = 'ef2bc7';

    public function testThrowsBadRequestWithMessageThrown(): void
    {
        $verifyUuid = new VerifyUuid();

        $route = Mockery::mock(Route::class);
        $route->allows('hasParameter')->andReturns(true);
        $route->allows('parameter')->with('id')->andReturns(self::USER_ID)->getMock();

        $request = Mockery::mock(Request::class);
        $request->allows('route')->withNoArgs()->andReturns($route)->getMock();

        $this->expectException(BadRequestHttpException::class);
        $verifyUuid->handle($request, static function (): void {
        });
    }
}
