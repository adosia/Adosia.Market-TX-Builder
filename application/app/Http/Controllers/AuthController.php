<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\JsonResponseTrait;

class AuthController
{
    use JsonResponseTrait;

    public function validateSignature(Request $request): JsonResponse
    {
        try {

            $payload = $request->only([
                'challenge',
                'stake_key',
                'signature_key',
                'signature_cbor',
                'is_testnet',
            ]);

            $isValid = shellExec(sprintf(
                'node %s \'%s\'',

                resource_path('nodejs/authValidateSignature.js'),
                json_encode($payload, JSON_THROW_ON_ERROR)
            ), __FUNCTION__, __FILE__, __LINE__) === 'true';

            return $this->successResponse([
                'is_valid' => $isValid,
            ]);

        } catch (Throwable $exception) {

            return $this->jsonException($exception);

        }
    }
}
