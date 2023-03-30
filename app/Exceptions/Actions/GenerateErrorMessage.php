<?php

namespace App\Exceptions\Actions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GenerateErrorMessage
{
    /**
     * @param array<string, mixed> $response
     * @param Throwable|JsonResponse|Response $e
     * @param int $status_code
     * @return array<string, mixed>
     */
    public static function execute(array $response, Throwable|JsonResponse|Response $e, int $status_code): array
    {
        switch ($status_code) {
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
                $response = self::handleUnprocessableError($response, $e);
                break;
            case 500:
                $response['error'] = 'Server error. Whoops!';
                break;
        }
        return $response;
    }

    /**
     * @param array<string, mixed> $response
     * @param Throwable|JsonResponse|Response $e
     * @return array<string, mixed>
     */
    private static function handleUnprocessableError(array $response, Throwable|JsonResponse|Response $e): array
    {
        if ($e instanceof JsonResponse) {
            $response['error'] = $e->original['message'];
            $response['errors'] = $e->original['errors'];
        } else {
            $response['error'] = 'Unprocessable entity error';
        }
        return $response;
    }
}
