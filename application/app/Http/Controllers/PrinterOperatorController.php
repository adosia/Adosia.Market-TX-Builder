<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use Throwable;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\BlockFrostTrait;
use App\Http\Traits\JsonResponseTrait;

class PrinterOperatorController extends Controller
{
    use JsonResponseTrait, BlockFrostTrait;

    public function purchaseOrderMakeOffer(Request $request): JsonResponse
    {
        $tempDir = null;

        try {

            // Initialise temp directory
            $tempDir = createTempDir(__FUNCTION__);

            // Export protocol parameters
            exportProtocolParams($tempDir);

            // Find the purchase order utxo
            /**
             * NOTES: Blockfrost does not return the correct index on the first api call,
             * so we have to make 2 redundant api calls to get the txIndex
             */
            $assetId = env('PURCHASE_ORDER_POLICY_ID') . bin2hex($request->po_name);
            $assetTransactions = $this->callBlockFrost("assets/$assetId/transactions?count=1&page=1&order=desc");
            if (!count($assetTransactions)) {
                throw new RuntimeException('Failed to load asset transactions');
            }
            $txId = $assetTransactions[0]['tx_hash'];
            $assetTransactionUTXOs = $this->callBlockFrost("txs/$txId/utxos");
            $txIndex = null;
            foreach ($assetTransactionUTXOs['outputs'] as $output) {
                foreach ($output['amount'] as $amount) {
                    if ($amount['unit'] === $assetId) {
                        $txIndex = $output['output_index'];
                    }
                }
                if (!is_null($txIndex)) {
                    break;
                }
            }
            if (is_null($txIndex)) {
                throw new RuntimeException('Failed to locate asset in smart contract');
            }
            $poUTXO = "$txId#$txIndex";

            // Export po utxo data
            $exportPOUTXOCommand = sprintf(
                '%s query utxo \\' . PHP_EOL .
                '--tx-in %s \\' . PHP_EOL .
                '--out-file %s/po.utxo \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                $poUTXO,
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($exportPOUTXOCommand, __FUNCTION__, __FILE__, __LINE__);
            $poUTXOData = json_decode(
                file_get_contents(sprintf('%s/po.utxo', $tempDir)),
                true, 512, JSON_THROW_ON_ERROR
            )[$poUTXO];

            // Parse required inline datum values
            $inlineDatum = $poUTXOData['inlineDatum'];
            $customerPKH = $inlineDatum['fields'][0]['fields'][0]['bytes'];
            $customerStakeKey = $inlineDatum['fields'][0]['fields'][1]['bytes'];
            $customerAddress = $this->buildAddress($customerPKH, $customerStakeKey);
            $poMinUTXO = (int) $poUTXOData['value']['lovelace'];


        } catch (Throwable $exception) {

            return $this->jsonException($exception);

        } finally {

            if ($tempDir) {
                rrmdir($tempDir);
            }

        }
    }

    /**
     * @throws AppException
     */
    private function buildAddress(string $pkh, string $stakeKey = ''): string
    {
        $stakePrefix = isTestnet() ? '00' : '01';
        $noStakePrefix = isTestnet() ? '60' : '61';
        $buildAddressCommand = sprintf(
            'echo "%s%s%s" | %s addr%s',

            empty(trim($stakeKey)) ? $noStakePrefix : $stakePrefix,
            $pkh,
            $stakeKey,
            BECH32,
            isTestnet() ? '_test' : '',
        );
        return shellExec($buildAddressCommand, __FUNCTION__, __FILE__, __LINE__);
    }
}
