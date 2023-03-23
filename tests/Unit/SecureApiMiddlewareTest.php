<?php

namespace Tests\Unit;

use App\Dtos\User;
use App\Http\Middleware\SecureApi;
use App\Services\Interfaces\JwtTokenProviderInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
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

        $this->mock(JwtTokenProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->andReturn(new User(1, 'bar'));
        });

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn() => $response;

        self::assertSame($response, $middleware->handle($request, $next));
    }

    public function testHandleValidTokenAdminGuardAllowAdminReturnResponse(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('foobar');

        $this->mock(JwtTokenProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->andReturn(new User(1, 'bar', true));
        });

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn() => $response;

        self::assertSame($response, $middleware->handle($request, $next, 'admin'));
    }

    public function testHandleValidTokenAdminGuardRefuseNonAdminException(): void
    {
        $this->expectException(UnauthorizedException::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('foobar');

        $this->mock(JwtTokenProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->andReturn(new User(1, 'bar', false));
        });

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn() => $response;

        $middleware->handle($request, $next, 'admin');
    }

    public function testFailHandleEmptyTokenException(): void
    {
        $this->expectException(AuthenticationException::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('');

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn() => $response;

        $middleware->handle($request, $next);
    }

    public function testFailHandleValidTokenFailedAuthenticateUserException(): void
    {
        $this->expectException(AuthenticationException::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('bearerToken')->andReturn('foobar');

        $this->mock(JwtTokenProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->andReturnNull();
        });

        $middleware = new SecureApi();

        $response = Mockery::mock(Response::class);
        $next = static fn() => $response;

        $middleware->handle($request, $next);
    }

}
