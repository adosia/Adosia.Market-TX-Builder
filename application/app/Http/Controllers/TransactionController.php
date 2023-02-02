<?php

namespace App\Http\Controllers;

use Throwable;
use JsonException;
use Illuminate\Http\Request;
use App\Exceptions\AppException;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\JsonResponseTrait;

class TransactionController
{
    use JsonResponseTrait;

    public function submit(Request $request): JsonResponse
    {
        $tempDir = null;

        try {

            // Initialise temp directory
            $tempDir = createTempDir(__FUNCTION__);

            // Generate signed tx file
            $this->buildSignedTx($tempDir, $request->signed_transaction_cbor);

            // Calculate tx id
            $txId = $this->calculateTxId($tempDir);

            // Submit tx
            $this->submitTx($tempDir);

            // Success
            return $this->successResponse([
                'tx_id' => $txId,
            ]);

        } catch (Throwable $exception) {

            return $this->jsonException($exception);

        } finally {

            if ($tempDir) {
                rrmdir($tempDir);
            }

        }
    }

    /**
     * @throws JsonException
     */
    private function buildSignedTx(
        string $tempDir,
        string $signedTransactionCBOR,
    ): void
    {
        file_put_contents(
            sprintf('%s/tx.signed', $tempDir),
            json_encode([
                'type' => TX_SIGNED_TYPE,
                'description' => TX_SIGNED_DESCRIPTION,
                'cborHex' => $signedTransactionCBOR,
            ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
        );
    }

    /**
     * @throws AppException
     */
    private function calculateTxId(string $tempDir): string
    {
        $txIdCommand = sprintf(
            '%s transaction txid \\' . PHP_EOL .
            '--tx-file %s/tx.signed',

            CARDANO_CLI,
            $tempDir,
        );

        return shellExec($txIdCommand, __FUNCTION__, __FILE__, __LINE__);
    }

    /**
     * @throws AppException
     */
    private function submitTx(string $tempDir): void
    {
        $txSubmitCommand = sprintf(
            '%s transaction submit \\' . PHP_EOL .
            '--tx-file %s/tx.signed \\' . PHP_EOL .
            '%s',

            CARDANO_CLI,
            $tempDir,
            cardanoNetworkFlag(),
        );

        shellExec($txSubmitCommand, __FILE__, __FILE__, __LINE__);
    }
}
