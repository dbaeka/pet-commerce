<?php

namespace App\Exceptions\Actions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TransformException
{
    public static function execute(Throwable|JsonResponse|Response $e): JsonResponse
    {
        $status_code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $response = self::getDefaultResponseArray();

        $response = GenerateErrorMessage::execute($response, $e, $status_code);

        $response = self::handleInjectDebugInfo($response, $e);
        return response()->json($response, $status_code);
    }

    /**
     * @return array<string, mixed>>
     */
    private static function getDefaultResponseArray(): array
    {
        return [
            'error' => 'Unknown error',
            'errors' => [],
            'data' => [],
            'success' => 0
        ];
    }

    /**
     * @param array<string,mixed> $response
     * @param Throwable|JsonResponse|Response $e
     * @return array<string, mixed>
     */
    private static function handleInjectDebugInfo(array $response, Throwable|JsonResponse|Response $e): array
    {
        if (config('app.debug') && $e instanceof Throwable) {
            $response['trace'] = $e->getTrace();
            $response['code'] = $e->getCode();
        }
        return $response;
    }
}
