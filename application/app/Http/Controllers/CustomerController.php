<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use RuntimeException;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\BlockFrostTrait;
use App\Http\Traits\JsonResponseTrait;

class CustomerController extends Controller
{
    use JsonResponseTrait, BlockFrostTrait;

    public function purchaseOrderPrintDesign(Request $request): JsonResponse
    {
        $tempDir = null;

        try {

            // Initialise temp directory
            $tempDir = createTempDir(__FUNCTION__);

            // Export protocol parameters
            exportProtocolParams($tempDir);

            // Generate mint redeemer
            file_put_contents(
                "$tempDir/mint_redeemer.json",
                json_encode([
                    'constructor' => 0,
                    'fields' => [[
                        'constructor' => 0,
                        'fields' => [[
                            'int' => 0,
                        ]],
                    ]],
                ], JSON_THROW_ON_ERROR),
            );

            // Parse inline datum of marketplace contract
            $parseMarketplaceContractInlineDatumCommand = sprintf(
                '%s query utxo \\' . PHP_EOL .
                '--address %s \\' . PHP_EOL .
                '--out-file %s/marketplace_datum.json \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                env('MARKETPLACE_CONTRACT_SCRIPT_ADDRESS'),
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($parseMarketplaceContractInlineDatumCommand, __FUNCTION__, __FILE__, __LINE__);
            $designContractInlineDatum = json_decode(
                file_get_contents(sprintf('%s/marketplace_datum.json', $tempDir)),
                true, 512, JSON_THROW_ON_ERROR,
            );

            // Parse script info
            $scriptTxIn = null;
            $currentPONumber = null;
            $poReturnMinUTXO = null;
            $marketplaceDatum = null;
            $designerAddress = null;
            $designerLovelaceAmount = null;
            $designIsFree = null;
            foreach ($designContractInlineDatum as $utxo => $utxoData) {
                if (isset($utxoData['value'][env('DESIGN_POLICY_ID')][bin2hex($request->design_name)])) {
                    $scriptTxIn = $utxo;
                    $marketplaceDatum = $utxoData['inlineDatum'];
                    $currentPONumber = (int) $utxoData['inlineDatum']['fields'][3]['int'];
                    $poReturnMinUTXO = (int) $utxoData['value']['lovelace'];
                    $designIsFree = ((int) $utxoData['inlineDatum']['fields'][6]['int'] === 1);
                    if (!$designIsFree) {
                        $designerAddress = $this->buildAddress(
                            $utxoData['inlineDatum']['fields'][0]['bytes'], // PKH
                            $utxoData['inlineDatum']['fields'][1]['bytes']  // Stake Key
                        );
                        $designerLovelaceAmount = (int) $utxoData['inlineDatum']['fields'][5]['int'];
                    }
                }
            }
            if (is_null($scriptTxIn)) {
                throw new RuntimeException('Design not found');
            }
            $nextPONumber = ($currentPONumber + 1);

            // Generate po mint name
            $poMintName = $request->design_name . '_' . $currentPONumber;

            // Generate printing pool datum
            file_put_contents(
                "$tempDir/printing_pool_datum.json",
                json_encode([
                    'constructor' => 0,
                    'fields' => [[
                        'constructor' => 0,
                        'fields' => [
                            [ 'bytes' => $request->customer_pkh ],
                            [ 'bytes' => $request->customer_stake_key ],
                            [ 'list' => [[ 'int' => 1 ]] ],
                            [ 'bytes' => bin2hex($poMintName) ],
                        ],
                    ]],
                ], JSON_THROW_ON_ERROR),
            );

            // Calculate minUTXO
            $minUTXOCommand = sprintf(
                '%s transaction calculate-min-required-utxo \\' . PHP_EOL .
                '%s \\' . PHP_EOL .
                '--protocol-params-file %s/protocol.json \\' . PHP_EOL .
                '--tx-out="%s + 5000000 + 1 %s.%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/printing_pool_datum.json ' .
                '| tr -dc \'0-9\'',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                env('PRINTING_POOL_CONTRACT_SCRIPT_ADDRESS'),
                env('PURCHASE_ORDER_POLICY_ID'),
                bin2hex($poMintName),
                $tempDir,
            );
            $poMinUTXO = shellExec($minUTXOCommand, __FUNCTION__, __FILE__, __LINE__);

            // Generate input transaction ids
            $txIns = '';
            foreach ($request->customer_input_tx_ids as $txId) {
                $txIns .= '--tx-in ' . $txId . ' \\' . PHP_EOL;
            }

            // Generate marketplace nft return output
            $returnMarketplaceNFT = sprintf(
                '%s + %d + 1 %s.%s',

                env('MARKETPLACE_CONTRACT_SCRIPT_ADDRESS'),
                $poReturnMinUTXO,
                env('DESIGN_POLICY_ID'),
                bin2hex($request->design_name),
            );

            // Generate po nft output
            $poNFTNameOutput = sprintf(
                '1 %s.%s',

                env('PURCHASE_ORDER_POLICY_ID'),
                bin2hex($poMintName),
            );
            $poNFTPrintingPoolOutput = sprintf(
                '%s + %d + %s',

                env('PRINTING_POOL_CONTRACT_SCRIPT_ADDRESS'),
                $poMinUTXO,
                $poNFTNameOutput,
            );

            // Generate marketplace datum
            $marketplaceDatum['fields'][3]['int'] = $nextPONumber;
            file_put_contents(
                "$tempDir/token_print_datum.json",
                json_encode($marketplaceDatum, JSON_THROW_ON_ERROR),
            );

            // Workout designer payment
            if (!$designIsFree && $designerLovelaceAmount >= 1000000) {
                $designerPaymentOut = '--tx-out="' . $designerAddress . ' + ' . $designerLovelaceAmount . '" \\' . PHP_EOL;
            } else {
                $designerPaymentOut = '\\' . PHP_EOL;
            }

            // Build print command
            $printPOCommand = sprintf(
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
                '--spending-reference-tx-in-redeemer-file %s/mint_redeemer.json \\' . PHP_EOL .
                '--tx-out="%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/token_print_datum.json \\' . PHP_EOL .
                '--tx-out="%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/printing_pool_datum.json \\' . PHP_EOL .
                '%s' .
                '--mint="%s" \\' . PHP_EOL .
                '--mint-tx-in-reference="%s" \\' . PHP_EOL .
                '--mint-plutus-script-v2 \\' . PHP_EOL .
                '--policy-id="%s" \\' . PHP_EOL .
                '--mint-reference-tx-in-redeemer-file %s/mint_redeemer.json \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                $tempDir,
                $request->customer_change_address,
                $request->customer_collateral,
                $txIns,
                $scriptTxIn,
                env('MARKETPLACE_LOCKING_REFERENCE_TX_ID'),
                $tempDir,
                $returnMarketplaceNFT,
                $tempDir,
                $poNFTPrintingPoolOutput,
                $tempDir,
                $designerPaymentOut,
                $poNFTNameOutput,
                env('MARKETPLACE_MINTING_REFERENCE_TX_ID'),
                env('PURCHASE_ORDER_POLICY_ID'),
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($printPOCommand, __FUNCTION__, __FILE__, __LINE__);

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

            empty($stakeKey) ? $noStakePrefix : $stakePrefix,
            $pkh,
            $stakeKey,
            BECH32,
            isTestnet() ? '_test' : '',
        );
        return shellExec($buildAddressCommand, __FUNCTION__, __FILE__, __LINE__);
    }
}
