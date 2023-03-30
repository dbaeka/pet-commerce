<?php

namespace Tests\Unit;

use App\DataObjects\User;
use App\Http\Middleware\SecureApi;
use App\Services\Jwt\AuthenticateWithToken;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecureApiMiddlewareTest extends TestCase
{
    public function testHandleValidTokenNoGuardReturnResponse(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('foobar');

        $this->mock(AuthenticateWithToken::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute')
                ->once()
                ->andReturn(User::from(['id' => 100]));
        });

        Auth::shouldReceive('onceUsingId');

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn () => $response;

        self::assertSame($response, $middleware->handle($request, $next));
    }

    public function testHandleValidTokenAdminGuardAllowAdminReturnResponse(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('foobar');

        $this->mock(AuthenticateWithToken::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute')
                ->once()
                ->andReturn(User::from([
                    'id' => 100,
                    'is_admin' => true
                ]));
        });

        Auth::shouldReceive('onceUsingId');

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn () => $response;

        self::assertSame($response, $middleware->handle($request, $next, 'admin'));
    }

    public function testHandleValidTokenAdminGuardRefuseNonAdminException(): void
    {
        $this->expectException(UnauthorizedException::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('foobar');

        $this->mock(AuthenticateWithToken::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute')
                ->once()
                ->andReturn(User::from([
                    'id' => 100,
                    'is_admin' => false
                ]));
        });

        Auth::shouldReceive('onceUsingId');

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn () => $response;

        $middleware->handle($request, $next, 'admin');
    }

    public function testFailHandleEmptyTokenException(): void
    {
        $this->mock(AuthenticateWithToken::class);

        $this->expectException(AuthenticationException::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('');

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn () => $response;

        $middleware->handle($request, $next);
    }

    public function testFailHandleValidTokenFailedAuthenticateUserException(): void
    {
        $this->expectException(AuthenticationException::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('foobar');

        $this->mock(AuthenticateWithToken::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute')
                ->once()
                ->andReturnNull();
        });

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn () => $response;

        $middleware->handle($request, $next);
    }
}
