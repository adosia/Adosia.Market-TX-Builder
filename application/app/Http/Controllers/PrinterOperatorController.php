<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use App\Exceptions\AppException;
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

            // Export po utxo data
            $exportPOUTXOCommand = sprintf(
                '%s query utxo \\' . PHP_EOL .
                '--tx-in %s \\' . PHP_EOL .
                '--out-file %s/po.utxo \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                $request->po_utxo,
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($exportPOUTXOCommand, __FUNCTION__, __FILE__, __LINE__);
            $poUTXOData = json_decode(
                file_get_contents(sprintf('%s/po.utxo', $tempDir)),
                true, 512, JSON_THROW_ON_ERROR
            )[$request->po_utxo];

            // Generate input transaction ids
            $txIns = '';
            foreach ($request->printer_operator_input_tx_ids as $txId) {
                $txIns .= '--tx-in ' . $txId . ' \\' . PHP_EOL;
            }

            // Generate make offer information datum
            $inlineDatum = $poUTXOData['inlineDatum'];
            $inlineDatum['constructor'] = 1;
            $inlineDatum['fields'][0]['fields'][4]['bytes'] = $request->printer_operator_pkh;
            $inlineDatum['fields'][0]['fields'][5]['bytes'] = $request->printer_operator_stake_key;
            $inlineDatum['fields'][0]['fields'][6]['int'] = $request->offer_amount;
            $inlineDatum['fields'][0]['fields'][7]['int'] = strtotime($request->delivery_date) * 1000;
            file_put_contents(
                "$tempDir/make_offer_information_datum.json",
                json_encode($inlineDatum, JSON_THROW_ON_ERROR),
            );

            // Build make offer command
            $makeOfferCommand = sprintf(
                '%s transaction build \\' . PHP_EOL .
                '%s \\' . PHP_EOL .
                '--protocol-params-file %s/protocol.json \\' . PHP_EOL .
                '--out-file %s/tx.draft \\' . PHP_EOL .
                '--change-address %s \\' . PHP_EOL .
                '%s' .
                '--tx-out="%s + 2000000" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/make_offer_information_datum.json \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                $tempDir,
                $request->printer_operator_change_address,
                $txIns,
                env('PRINTING_POOL_CONTRACT_SCRIPT_ADDRESS'),
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($makeOfferCommand, __FUNCTION__, __FILE__, __LINE__);

            // Read the draft tx
            $draftTx = json_decode(file_get_contents(sprintf(
                "%s/tx.draft",
                $tempDir,
            )), true, 512, JSON_THROW_ON_ERROR);

            // Success
            return $this->successResponse([
                'transaction' => $draftTx['cborHex'],
            ]);

        } catch (Throwable $exception) {

            return $this->jsonException($exception);

        } finally {

            if ($tempDir) {
                rrmdir($tempDir);
            }

        }
    }

    public function purchaseOrderRemoveOffer(Request $request): JsonResponse
    {
        $tempDir = null;

        try {

            // Initialise temp directory
            $tempDir = createTempDir(__FUNCTION__);

            // Export protocol parameters
            exportProtocolParams($tempDir);

            // Export po utxo data
            $exportOfferUTXOCommand = sprintf(
                '%s query utxo \\' . PHP_EOL .
                '--tx-in %s \\' . PHP_EOL .
                '--out-file %s/offer.utxo \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                $request->offer_utxo,
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($exportOfferUTXOCommand, __FUNCTION__, __FILE__, __LINE__);
            $offerUTXOData = json_decode(
                file_get_contents(sprintf('%s/offer.utxo', $tempDir)),
                true, 512, JSON_THROW_ON_ERROR
            )[$request->offer_utxo];

            // Parse required inline datum values
            $inlineDatum = $offerUTXOData['inlineDatum'];
            $printerOperatorPKH = $inlineDatum['fields'][0]['fields'][4]['bytes'];
            $printerOperatorStakeKey = $inlineDatum['fields'][0]['fields'][5]['bytes'];
            $printerOperatorAddress = $this->buildAddress($printerOperatorPKH, $printerOperatorStakeKey);
            $offerMinUTXO = (int) $offerUTXOData['value']['lovelace'];

            // Generate remove redeemer
            file_put_contents(
                "$tempDir/remove_redeemer.json",
                json_encode([
                    'constructor' => 0,
                    'fields' => [],
                ], JSON_THROW_ON_ERROR),
            );

            // Generate refund offer minutxo
            $offerRefundOutput = sprintf(
                '%s + %d',

                $printerOperatorAddress,
                $offerMinUTXO,
            );

            // Generate input transaction ids
            $txIns = '';
            foreach ($request->printer_operator_input_tx_ids as $txId) {
                $txIns .= '--tx-in ' . $txId . ' \\' . PHP_EOL;
            }

            // Build remove po command
            $removePOCommand = sprintf(
                '%s transaction build \\' . PHP_EOL .
                '%s \\' . PHP_EOL .
                '--protocol-params-file %s/protocol.json \\' . PHP_EOL .
                '--out-file %s/tx.draft \\' . PHP_EOL .
                '--change-address %s \\' . PHP_EOL .
                '--tx-in-collateral="%s" \\' . PHP_EOL .
                '%s' .
                '--tx-in %s \\' . PHP_EOL .
                '--spending-tx-in-reference="%s" \\' . PHP_EOL .
                '--spending-plutus-script-v2 \\' . PHP_EOL .
                '--spending-reference-tx-in-inline-datum-present \\' . PHP_EOL .
                '--spending-reference-tx-in-redeemer-file %s/remove_redeemer.json \\' . PHP_EOL .
                '--tx-out="%s" \\' . PHP_EOL .
                '--required-signer-hash %s \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                $tempDir,
                $request->printer_operator_change_address,
                $request->printer_operator_collateral,
                $txIns,
                $request->offer_utxo,
                env('PRINTING_POOL_LOCKING_REFERENCE_TX_ID'),
                $tempDir,
                $offerRefundOutput,
                $printerOperatorPKH,
                cardanoNetworkFlag(),
            );
            shellExec($removePOCommand, __FUNCTION__, __FILE__, __LINE__);

            // Read the draft tx
            $draftTx = json_decode(file_get_contents(sprintf(
                "%s/tx.draft",
                $tempDir,
            )), true, 512, JSON_THROW_ON_ERROR);

            // Success
            return $this->successResponse([
                'transaction' => $draftTx['cborHex'],
            ]);

        } catch (Throwable $exception) {

            return $this->jsonException($exception);

        } finally {

            if ($tempDir) {
                rrmdir($tempDir);
            }

        }
    }

    public function purchaseOrderSetShipped(Request $request): JsonResponse
    {
        $tempDir = null;

        try {

            // Initialise temp directory
            $tempDir = createTempDir(__FUNCTION__);

            // Export protocol parameters
            exportProtocolParams($tempDir);

            // Export po utxo data
            $exportPOUTXOCommand = sprintf(
                '%s query utxo \\' . PHP_EOL .
                '--tx-in %s \\' . PHP_EOL .
                '--out-file %s/offer.utxo \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                $request->po_utxo,
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($exportPOUTXOCommand, __FUNCTION__, __FILE__, __LINE__);
            $poUTXOData = json_decode(
                file_get_contents(sprintf('%s/offer.utxo', $tempDir)),
                true, 512, JSON_THROW_ON_ERROR
            )[$request->po_utxo];

            // Parse required inline datum values
            $poMinUTXO = (int) $poUTXOData['value']['lovelace'];
            $inlineDatum = $poUTXOData['inlineDatum'];
            $poName = $inlineDatum['fields'][0]['fields'][3]['bytes'];
            $printerOperatorPKH = $inlineDatum['fields'][0]['fields'][4]['bytes'];

            // Generate shipping information datum
            file_put_contents(
                "$tempDir/shipping_information_datum.json",
                json_encode([
                    'constructor' => 3,
                    'fields' => [[
                        'constructor' => 0,
                        'fields' => [
                            [ 'bytes' => $inlineDatum['fields'][0]['fields'][0]['bytes'] ],
                            [ 'bytes' => $inlineDatum['fields'][0]['fields'][1]['bytes'] ],
                            [ 'bytes' => $inlineDatum['fields'][0]['fields'][4]['bytes'] ],
                            [ 'bytes' => $inlineDatum['fields'][0]['fields'][5]['bytes'] ],
                            [ 'int' => $inlineDatum['fields'][0]['fields'][6]['int'] ],
                            [ 'bytes' => $inlineDatum['fields'][0]['fields'][3]['bytes'] ],
                        ],
                    ]],
                ], JSON_THROW_ON_ERROR),
            );

            // Generate shipping redeemer
            file_put_contents(
                "$tempDir/shipping_redeemer.json",
                json_encode([
                    'constructor' => 5,
                    'fields' => [],
                ], JSON_THROW_ON_ERROR),
            );

            // Generate po nft output
            $poNFTNameOutput = sprintf(
                '1 %s.%s',

                env('PURCHASE_ORDER_POLICY_ID'),
                $poName,
            );
            $poPrintingPoolOutput = sprintf(
                '%s + %d + %s',

                env('PRINTING_POOL_CONTRACT_SCRIPT_ADDRESS'),
                $poMinUTXO,
                $poNFTNameOutput,
            );

            // Generate input transaction ids
            $txIns = '';
            foreach ($request->printer_operator_input_tx_ids as $txId) {
                $txIns .= '--tx-in ' . $txId . ' \\' . PHP_EOL;
            }

            // Build set shipped command
            $setShippedCommand = sprintf(
                '%s transaction build \\' . PHP_EOL .
                '%s \\' . PHP_EOL .
                '--protocol-params-file %s/protocol.json \\' . PHP_EOL .
                '--out-file %s/tx.draft \\' . PHP_EOL .
                '--change-address %s \\' . PHP_EOL .
                '%s' .
                '--tx-in-collateral %s \\' . PHP_EOL .
                '--tx-in %s \\' . PHP_EOL .
                '--spending-tx-in-reference="%s" \\' . PHP_EOL .
                '--spending-plutus-script-v2 \\' . PHP_EOL .
                '--spending-reference-tx-in-inline-datum-present \\' . PHP_EOL .
                '--spending-reference-tx-in-redeemer-file %s/shipping_redeemer.json \\' . PHP_EOL .
                '--tx-out="%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/shipping_information_datum.json \\' . PHP_EOL .
                '--required-signer-hash %s \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                $tempDir,
                $request->printer_operator_change_address,
                $txIns,
                $request->printer_operator_collateral,
                $request->po_utxo,
                env('PRINTING_POOL_LOCKING_REFERENCE_TX_ID'),
                $tempDir,
                $poPrintingPoolOutput,
                $tempDir,
                $printerOperatorPKH,
                cardanoNetworkFlag(),
            );
            shellExec($setShippedCommand, __FUNCTION__, __FILE__, __LINE__);

            // Read the draft tx
            $draftTx = json_decode(file_get_contents(sprintf(
                "%s/tx.draft",
                $tempDir,
            )), true, 512, JSON_THROW_ON_ERROR);

            // Success
            return $this->successResponse([
                'transaction' => $draftTx['cborHex'],
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
