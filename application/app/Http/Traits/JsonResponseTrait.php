<?php

namespace App\Http\Traits;

use Throwable;
use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    public function successResponse(mixed $data, int $statusCode = 200): JsonResponse
    {
        return $this->response(['data' => $data], $statusCode);
    }

    public function errorResponse(mixed $data, int $statusCode = 500): JsonResponse
    {
        return $this->response(['error' => $data], $statusCode);
    }

    public function jsonException(Throwable $exception): JsonResponse
    {
        return $this->errorResponse([
            'message' => $exception->getMessage(),
            'file' => basename($exception->getFile()),
            'line' => $exception->getLine(),
            'previous' => ($exception->getPrevious() ? [
                'error' => $exception->getPrevious()->getMessage(),
                'file' => basename($exception->getPrevious()->getFile()),
                'line' => $exception->getPrevious()->getLine(),
            ] : null)
        ]);
    }

    private function response(array $data, int $statusCode): JsonResponse
    {
        return response()
            ->json($data, $statusCode);
    }
}
