<?php

namespace App\Http\Controllers;

use Throwable;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\BlockFrostTrait;
use App\Http\Traits\JsonResponseTrait;

class DesignerController extends Controller
{
    use JsonResponseTrait, BlockFrostTrait;

    public function mintDesign(Request $request): JsonResponse
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
                    'fields' => [],
                ], JSON_THROW_ON_ERROR),
            );

            // Generate metadata json
            file_put_contents(
                "$tempDir/metadata.json",
                json_encode((object) [
                    DESIGN_METADATA_INDEX => (object) [
                        'name' => substr($request->name, 0, 64),
                        'image' => $request->image,
                        'glb_model' => $request->glb_model,
                        'stl_models' => $request->stl_models,
                    ]
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            );

            // Parse inline datum of design contract
            $parseDesignContractInlineDatumCommand = sprintf(
                '%s query utxo \\' . PHP_EOL .
                '--address %s \\' . PHP_EOL .
                '--out-file %s/design_datum.json \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                env('DESIGN_CONTRACT_SCRIPT_ADDRESS'),
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($parseDesignContractInlineDatumCommand, __FUNCTION__, __FILE__, __LINE__);
            $designContractInlineDatum = json_decode(
                file_get_contents(sprintf('%s/design_datum.json', $tempDir)),
                true, 512, JSON_THROW_ON_ERROR,
            );

            // Parse script info
            $scriptTxIn = null;
            $currentDesignNumber = null;
            $designReturnMinUTXO = null;
            foreach ($designContractInlineDatum as $utxo => $utxoData) {
                if (isset($utxoData['value'][env('DESIGN_STARTER_POLICY_ID')][env('DESIGN_STARTER_ASSET_ID')])) {
                    $scriptTxIn = $utxo;
                    $currentDesignNumber = (int) $utxoData['inlineDatum']['fields'][1]['int'];
                    $designReturnMinUTXO = (int) $utxoData['value']['lovelace'];
                }
            }
            $nextDesignNumber = ($currentDesignNumber + 1);

            // Generate marketplace datum
            file_put_contents(
                "$tempDir/marketplace_datum.json",
                json_encode([
                    'constructor' => 0,
                    'fields' => [
                        [ 'bytes' => $request->designer_pkh ],
                        [ 'bytes' => $request->designer_stake_key ],
                        [ 'bytes' => bin2hex(env('DESIGN_PREFIX') . $currentDesignNumber) ],
                        [ 'int' => 0 ],
                        [ 'bytes' => env('PURCHASE_ORDER_POLICY_ID') ],
                        [ 'int' => $request->print_price_lovelace ],
                        [ 'int' => $request->print_price_lovelace > 1000000 ? 0 : 1 ], // is free?
                    ],
                ], JSON_THROW_ON_ERROR),
            );

            // Calculate minUTXO
            $minUTXOCommand = sprintf(
                '%s transaction calculate-min-required-utxo \\' . PHP_EOL .
                '%s \\' . PHP_EOL .
                '--protocol-params-file %s/protocol.json \\' . PHP_EOL .
                '--tx-out="%s + 5000000 + 1 %s.%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/marketplace_datum.json ' .
                '| tr -dc \'0-9\'',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                env('MARKETPLACE_CONTRACT_SCRIPT_ADDRESS'),
                env('DESIGN_POLICY_ID'),
                bin2hex(env('DESIGN_PREFIX') . $currentDesignNumber),
                $tempDir,
            );
            $designMinUTXO = shellExec($minUTXOCommand, __FUNCTION__, __FILE__, __LINE__);

            // Generate input transaction ids
            $txIns = '';
            foreach ($request->designer_input_tx_ids as $txId) {
                $txIns .= '--tx-in ' . $txId . ' \\' . PHP_EOL;
            }

            // Generate starter nft return output
            $returnStarterNFT = sprintf(
                '%s + %d + 1 %s.%s',

                env('DESIGN_CONTRACT_SCRIPT_ADDRESS'),
                $designReturnMinUTXO,
                env('DESIGN_STARTER_POLICY_ID'),
                env('DESIGN_STARTER_ASSET_ID'),
            );

            // Generate design nft output
            $designNFTNameOutput = sprintf(
                '1 %s.%s',

                env('DESIGN_POLICY_ID'),
                bin2hex(env('DESIGN_PREFIX') . $currentDesignNumber),
            );
            $designNFTMarketplaceOutput = sprintf(
                '%s + %d + %s',

                env('MARKETPLACE_CONTRACT_SCRIPT_ADDRESS'),
                $designMinUTXO,
                $designNFTNameOutput,
            );

            // Generate new design datum
            file_put_contents(
                "$tempDir/token_design_datum.json",
                json_encode([
                    'constructor' => 0,
                    'fields' => [
                        [ 'bytes' => env('DESIGN_POLICY_ID') ],
                        [ 'int' => $nextDesignNumber ],
                        [ 'bytes' => bin2hex(env('DESIGN_PREFIX')) ],
                    ]
                ], JSON_THROW_ON_ERROR),
            );

            // Build mint & lock command
            $mintAndLockCommand = sprintf(
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
                '--tx-out-inline-datum-file %s/token_design_datum.json \\' . PHP_EOL .
                '--tx-out="%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/marketplace_datum.json \\' . PHP_EOL .
                '--mint="%s" \\' . PHP_EOL .
                '--mint-tx-in-reference="%s" \\' . PHP_EOL .
                '--mint-plutus-script-v2 \\' . PHP_EOL .
                '--policy-id="%s" \\' . PHP_EOL .
                '--mint-reference-tx-in-redeemer-file %s/mint_redeemer.json \\' . PHP_EOL .
                '--metadata-json-file %s/metadata.json \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                $tempDir,
                $request->designer_change_address,
                $request->designer_collateral,
                $txIns,
                $scriptTxIn,
                env('DESIGN_CONTRACT_LOCKING_REFERENCE_TX_ID'),
                $tempDir,
                $returnStarterNFT,
                $tempDir,
                $designNFTMarketplaceOutput,
                $tempDir,
                $designNFTNameOutput,
                env('DESIGN_CONTRACT_MINTING_REFERENCE_TX_ID'),
                env('DESIGN_POLICY_ID'),
                $tempDir,
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($mintAndLockCommand, __FUNCTION__, __FILE__, __LINE__);

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

    public function mintUpdate(Request $request): JsonResponse
    {
        $tempDir = null;

        try {

            // Initialise temp directory
            $tempDir = createTempDir(__FUNCTION__);

            // Export protocol parameters
            exportProtocolParams($tempDir);

            // Find the design utxo
            /**
             * NOTES: Blockfrost does not return the correct index on the first api call,
             * so we have to make 2 redundant api calls to get the txIndex
             */
            $assetId = env('DESIGN_POLICY_ID') . bin2hex($request->design_name);
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
            $designUTXO = "$txId#$txIndex";

            // Export design utxo data
            $exportDesignUTXOCommand = sprintf(
                '%s query utxo \\' . PHP_EOL .
                '--tx-in %s \\' . PHP_EOL .
                '--out-file %s/design.utxo \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                $designUTXO,
                $tempDir,
                cardanoNetworkFlag(),
            );
            shellExec($exportDesignUTXOCommand, __FUNCTION__, __FILE__, __LINE__);
            $designUTXOData = json_decode(
                file_get_contents(sprintf('%s/design.utxo', $tempDir)),
                true, 512, JSON_THROW_ON_ERROR
            )[$designUTXO];

            // Generate new marketplace datum
            $inlineDatum = $designUTXOData['inlineDatum'];
            $inlineDatum['fields'][5]['int'] = ($request->is_free ? 1 : $request->print_price_lovelace);
            $inlineDatum['fields'][6]['int'] = ($request->is_free ? 1 : 0);
            file_put_contents(
                "$tempDir/updated_marketplace_datum.json",
                json_encode($inlineDatum, JSON_THROW_ON_ERROR),
            );

            // Calculate new minUTXO
            $minUTXOCommand = sprintf(
                '%s transaction calculate-min-required-utxo \\' . PHP_EOL .
                '%s \\' . PHP_EOL .
                '--protocol-params-file %s/protocol.json \\' . PHP_EOL .
                '--tx-out="%s + 5000000 + 1 %s.%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/updated_marketplace_datum.json ' .
                '| tr -dc \'0-9\'',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                env('MARKETPLACE_CONTRACT_SCRIPT_ADDRESS'),
                env('DESIGN_POLICY_ID'),
                bin2hex($request->design_name),
                $tempDir,
            );
            $updatedDesignMinUTXO = (int) shellExec($minUTXOCommand, __FUNCTION__, __FILE__, __LINE__);

            // Calculate minUTXO difference
            $oldDesignMinUTXO = (int) $designUTXOData['value']['lovelace'];
            $designMinUTXODelta = $updatedDesignMinUTXO - $oldDesignMinUTXO;

            // Generate update redeemer
            file_put_contents(
                "$tempDir/updated_redeemer.json",
                json_encode([
                    'constructor' => 1,
                    'fields' => [[
                        'constructor' => 0,
                        'fields' => [
                            [ 'int' => (max($designMinUTXODelta, 0)) ],
                        ],
                    ]],
                ], JSON_THROW_ON_ERROR),
            );

            // Generate input transaction ids
            $txIns = '';
            foreach ($request->designer_input_tx_ids as $txId) {
                $txIns .= '--tx-in ' . $txId . ' \\' . PHP_EOL;
            }

            // Generate design nft output
            $designNFTNameOutput = sprintf(
                '1 %s.%s',

                env('DESIGN_POLICY_ID'),
                bin2hex($request->design_name),
            );
            $designNFTMarketplaceOutput = sprintf(
                '%s + %d + %s',

                env('MARKETPLACE_CONTRACT_SCRIPT_ADDRESS'),
                max($oldDesignMinUTXO, $updatedDesignMinUTXO),
                $designNFTNameOutput,
            );

            // Update sale
            $updateSaleCommand = sprintf(
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
                '--spending-reference-tx-in-redeemer-file %s/updated_redeemer.json \\' . PHP_EOL .
                '--tx-out="%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/updated_marketplace_datum.json \\' . PHP_EOL .
                '--required-signer-hash %s \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                $tempDir,
                $request->designer_change_address,
                $request->designer_collateral,
                $txIns,
                $designUTXO,
                env('MARKETPLACE_LOCKING_REFERENCE_TX_ID'),
                $tempDir,
                $designNFTMarketplaceOutput,
                $tempDir,
                $designUTXOData['inlineDatum']['fields'][0]['bytes'],
                cardanoNetworkFlag(),
            );
            shellExec($updateSaleCommand, __FUNCTION__, __FILE__, __LINE__);

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
}
