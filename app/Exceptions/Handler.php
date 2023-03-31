<?php

namespace App\Exceptions;

use App\Exceptions\Actions\TransformToResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            return $this->handleApiException($request, $e);
        });
    }

    private function handleApiException(Request $request, Throwable|AuthenticationException|ValidationException $e): JsonResponse
    {
        $exception = $this->prepareException($e);

        if ($exception instanceof HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            /** @var AuthenticationException $auth_exception */
            $auth_exception = $e;
            $exception = $this->unauthenticated($request, $auth_exception);
        }

        if ($exception instanceof ValidationException) {
            /** @var ValidationException $val_exception */
            $val_exception = $e;
            $exception = $this->convertValidationExceptionToResponse($val_exception, $request);
            if ($exception instanceof JsonResponse) {
                $exception->original['message'] = 'Bad request';
            }
        }

        if ($exception instanceof UnauthorizedException) {
            $exception = $this->convertExceptionToResponse($e);
            $exception->setStatusCode(403);
        }


        if ($exception instanceof QueryException) {
            $exception = $this->convertExceptionToResponse($e);
            $exception->setStatusCode(422);
        }

        return TransformToResponse::execute($exception);
    }
}
