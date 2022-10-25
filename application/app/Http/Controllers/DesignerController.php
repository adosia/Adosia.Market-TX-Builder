<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\JsonResponseTrait;

class DesignerController extends Controller
{
    use JsonResponseTrait;

    public function mintDesign(Request $request): JsonResponse
    {
        $tempDir = null;

        try {

            // Initialise temp directory
            $tempDir = createTempDir(__FUNCTION__);

            // Export protocol parameters
            exportProtocolParams($tempDir);

            // Build the asset name
            $assetName = $request->design_name_prefix . toShortHash(random_bytes(128));

            // Generate marketplace datum
            file_put_contents(
                "$tempDir/marketplace_datum.json",
                json_encode([
                    'constructor' => 0,
                    'fields' => [
                        [ 'bytes' => $request->designer_pkh ],
                        [ 'bytes' => $request->designer_stake_key ],
                        [ 'bytes' => config('adosia.policies.designer.policy_id') ],
                        [ 'bytes' => bin2hex($assetName) ],
                        [ 'int' => 0 ],
                        [ 'bytes' => config('adosia.policies.purchase_order.policy_id') ],
                        [ 'bytes' => bin2hex($request->design_name_prefix) ],
                        [ 'int' => $request->print_price_lovelace ],
                    ]
                ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
            );

            // Generate designer policy script
            file_put_contents(
                "$tempDir/mint_policy.script",
                config('adosia.policies.designer.script'),
            );

            // Generate metadata json
            file_put_contents(
                "$tempDir/metadata.json",
                json_encode([
                    721 => [
                        'name' => $assetName,
                        'image' => $request->thumbnail,
                        'glb_model' => $request->glb_model,
                        'stl_models' => $request->stl_models,
                    ]
                ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
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
                config('adosia.contracts.marketplace.script_address'),
                config('adosia.policies.designer.policy_id'),
                bin2hex($assetName),
                $tempDir,
            );
            $minUTXO = shellExec($minUTXOCommand, __FUNCTION__, __FILE__, __LINE__);

            // Generate input transaction ids
            $txIns = '';
            foreach ($request->designer_input_tx_ids as $txId) {
                $txIns .= '--tx-in ' . $txId . ' \\' . PHP_EOL;
            }

            // Generate script output
            $scriptOutput = sprintf(
                '%s + %d + 1 %s.%s',

                config('adosia.contracts.marketplace.script_address'),
                $minUTXO,
                config('adosia.policies.designer.policy_id'),
                bin2hex($assetName),
            );

            // Generate designer policy vkey pkh
            file_put_contents(
                "$tempDir/designer_policy.vkey",
                config('adosia.policies.designer.vkey'),
            );
            $designerPolicyPKHCommand = sprintf(
                '%s address key-hash --payment-verification-key-file %s/designer_policy.vkey',

                CARDANO_CLI,
                $tempDir
            );
            $designerPolicyPKH = shellExec($designerPolicyPKHCommand, __FUNCTION__, __FILE__, __LINE__);

            // Build the command
            $mintCommand = sprintf(
                '%s transaction build \\' . PHP_EOL .
                '%s \\' . PHP_EOL .
                '--out-file %s/tx.draft \\' . PHP_EOL .
                '--change-address %s \\' . PHP_EOL .
                '%s' .
                '--tx-out="%s" \\' . PHP_EOL .
                '--tx-out-inline-datum-file %s/marketplace_datum.json \\' . PHP_EOL .
                '--mint-script-file %s/mint_policy.script \\' . PHP_EOL .
                '--mint="1 %s.%s" \\' . PHP_EOL .
                '--metadata-json-file %s/metadata.json \\' . PHP_EOL .
                '--required-signer-hash %s \\' . PHP_EOL .
                '--required-signer-hash %s \\' . PHP_EOL .
                '--cddl-format \\' . PHP_EOL .
                '%s',

                CARDANO_CLI,
                NETWORK_ERA,
                $tempDir,
                $request->designer_change_address,
                $txIns,
                $scriptOutput,
                $tempDir,
                $tempDir,
                config('adosia.policies.designer.policy_id'),
                bin2hex($assetName),
                $tempDir,
                $request->designer_pkh,
                $designerPolicyPKH,
                cardanoNetworkFlag(),
            );
            shellExec($mintCommand, __FUNCTION__, __FILE__, __LINE__);

            // Success
            $draftTx = json_decode(file_get_contents(sprintf(
                "%s/tx.draft",
                $tempDir,
            )), true, 512, JSON_THROW_ON_ERROR);
            return $this->successResponse($draftTx['cborHex']);

        } catch (Throwable $exception) {

            return $this->jsonException($exception);

        } finally {

            if ($tempDir) {
                rrmdir($tempDir);
            }

        }
    }
}
