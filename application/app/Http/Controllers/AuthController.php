<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\JsonResponseTrait;

class AuthController
{
    use JsonResponseTrait;

    function validateSignature(Request $request): JsonResponse
    {
        try {

            return $this->successResponse($request->all());

        } catch (Throwable $exception) {

            return $this->jsonException($exception);

        }
    }
}
