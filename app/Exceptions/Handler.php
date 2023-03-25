<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
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

        return $this->customApiResponse($exception);
    }

    private function customApiResponse(Throwable|JsonResponse|Response $e): JsonResponse
    {
        if (method_exists($e, 'getStatusCode')) {
            $statusCode = $e->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response['error'] = 'Unknown error';
        $response['errors'] = [];
        $response['data'] = [];
        $response['success'] = 0;

        switch ($statusCode) {
            case 401:
                $response['error'] = 'Failed to authorize user';
                break;
            case 403:
                $response['error'] = 'Forbidden';
                break;
            case 404:
                $response['error'] = 'Not Found';
                break;
            case 405:
                $response['error'] = 'Method Not Allowed';
                break;
            case 422:
                if ($e instanceof JsonResponse) {
                    $response['error'] = $e->original['message'];
                    $response['errors'] = $e->original['errors'];
                } else {
                    $response['error'] = 'Unprocessable entity error';
                }
                break;
            default:
                if ($statusCode == 500) {
                    $response['error'] = 'Server error. Whoops!';
                } elseif ($e instanceof Throwable) {
                    $response['error'] = $e->getMessage();
                }
                break;
        }

        if (config('app.debug') && $e instanceof Throwable) {
            $response['trace'] = $e->getTrace();
            $response['code'] = $e->getCode();
        }
        return response()->json($response, $statusCode);
    }
}
