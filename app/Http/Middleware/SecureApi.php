<?php

namespace App\Http\Middleware;

use App\DataObjects\User;
use App\Services\Jwt\AuthenticateWithToken;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SecureApi
{
    private User $user;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @param string $guard
     * @return Response
     * @throws AuthenticationException|Throwable
     */
    public function handle(Request $request, Closure $next, string $guard = ''): Response
    {
        $this->handleAuthentication($request);

        return $this->handleAuthorization($request, $next, $guard);
    }

    /**
     * @throws AuthenticationException
     */
    private function handleAuthentication(Request $request): void
    {
        if (empty($request->bearerToken())) {
            throw new AuthenticationException();
        }
        $user = app(AuthenticateWithToken::class)->execute($request->bearerToken());
        if (empty($user)) {
            throw new AuthenticationException('invalid bearer token');
        }
        $this->user = $user;
        Auth::onceUsingId($user->id);
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @param string $guard
     * @return Response
     * @throws Throwable
     */
    private function handleAuthorization(Request $request, Closure $next, string $guard): Response
    {
        if ($guard === 'admin') {
            throw_if(!$this->user->is_admin, new UnauthorizedException('not authorized to access admin'));
            return $next($request);
        }
        if ($guard === 'regular') {
            throw_if($this->user->is_admin, new UnauthorizedException('not authorized to access admin'));
            return $next($request);
        }
        return $next($request);
    }
}
