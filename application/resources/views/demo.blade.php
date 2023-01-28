<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Latheesan Kanesamoorthy">
    <title>TX Builder Demo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.2/css/bootstrap.min.css" integrity="sha512-CpIKUSyh9QX2+zSdfGP+eWLx23C8Dj9/XmHjZY2uDtfkdLGo0uY12jgcnkX9vXOgYajEKb/jiw67EYm+kBf+6g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.6.2/sweetalert2.min.css" integrity="sha512-5aabpGaXyIfdaHgByM7ZCtgSoqg51OAt8XWR2FHr/wZpTCea7ByokXbMX2WSvosioKvCfAGDQLlGDzuU6Nm37Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="p-3">

    <div class="container-fluid">

        <h4 class="mb-3">1. Connect Wallet <a href="/demo" class="btn btn-primary btn-sm">Change Wallet</a></h4>
        <div id="wallet-integration" class="d-flex justify-content-start align-items-center gap-3 mb-3">
            Detecting wallets ... please wait
        </div>

        <div id="demo-actions" style="display: none;">

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">2. [ Designer ] Mint & Lock Demo</h4>
                    <form id="mint-form">
                        <div class="mb-3">
                            <label for="name" class="form-label"><strong>Design Name</strong></label>
                            <input id="name" name="name" maxlength="64" placeholder="e.g. Space Rocket" type="text" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label"><strong>Thumbnail Image</strong></label>
                            <input id="image" name="image" aria-describedby="imageHelp" maxlength="64" placeholder="e.g. ipfs://xxx or ar://yyy" type="text" class="form-control form-control-sm" required>
                            <div id="imageHelp" class="form-text">This is a preview image of the design uploaded to either IPFS or Arweave</div>
                        </div>
                        <div class="mb-3">
                            <label for="glb_model" class="form-label"><strong>GLB Model</strong></label>
                            <input id="glb_model" name="glb_model" aria-describedby="glbModelHelp" maxlength="64" placeholder="e.g. ipfs://xxx or ar://yyy" type="text" class="form-control form-control-sm" required>
                            <div id="glbModelHelp" class="form-text">This is a GBL model of you design, uploaded to either IPFS or Arweave</div>
                        </div>
                        <div class="mb-3">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="stl_files" class="form-label mb-0"><strong>STL File</strong></label>
                                    <input id="stl_files" name="stl_files_1" aria-describedby="stlFilesHelp" maxlength="64" placeholder="e.g. ipfs://xxx or ar://yyy" type="text" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="print_quantities" class="form-label mb-0"><strong>Print Quantity</strong></label>
                                    <input id="print_quantities" name="print_quantities_1" aria-describedby="glbModelHelp" maxlength="10" placeholder="e.g. 2" type="number" min="1" step="1" class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="stl_files" class="form-label mb-0"><strong>STL File</strong></label>
                                    <input id="stl_files" name="stl_files_2" aria-describedby="stlFilesHelp" maxlength="64" placeholder="e.g. ipfs://xxx or ar://yyy" type="text" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label for="print_quantities" class="form-label mb-0"><strong>Print Quantity</strong></label>
                                    <input id="print_quantities" name="print_quantities_2" aria-describedby="glbModelHelp" maxlength="10" placeholder="e.g. 2" type="number" min="1" step="1" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="stl_files" class="form-label mb-0"><strong>STL File</strong></label>
                                    <input id="stl_files" name="stl_files_3" aria-describedby="stlFilesHelp" maxlength="64" placeholder="e.g. ipfs://xxx or ar://yyy" type="text" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label for="print_quantities" class="form-label mb-0"><strong>Print Quantity</strong></label>
                                    <input id="print_quantities" name="print_quantities_3" aria-describedby="glbModelHelp" maxlength="10" placeholder="e.g. 2" type="number" min="1" step="1" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div id="stlFilesHelp" class="form-text">
                                Provide hash for the STL (3D Print) files uploaded to either IPFS or Arweave and number of print quantity for each
                                <br>
                                You can specify up to 3 files during the demo (later this will be dynamic)
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input is_free" type="radio" name="is_free" value="yes" id="free" checked>
                                <label class="form-check-label" for="free">
                                    <strong>Free</strong> Design
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input is_free" type="radio" name="is_free" value="no" id="paid">
                                <label class="form-check-label" for="paid">
                                    <strong>Paid</strong> Design
                                </label>
                            </div>
                        </div>
                        <div class="mb-3" id="print_price_container" style="display: none;">
                            <label for="print_price" class="form-label"><strong>Print Price</strong></label>
                            <input id="print_price" name="print_price" aria-describedby="printPriceHelp" maxlength="10" placeholder="e.g. 15" type="number" min="1" class="form-control form-control-sm">
                            <div id="printPriceHelp" class="form-text">How much would you like to be paid in ₳DA, everytime this design is printed?</div>
                        </div>
                        <button type="submit" class="btn mint-button btn-primary">
                            Designer: <strong>Mint &amp; Send to Marketplace</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">3. [ Designer ] Update Price Demo</h4>
                    <form id="update-form">
                        <div class="mb-3">
                            <label for="design_name" class="form-label"><strong>Adosia Design Name</strong></label>
                            <input id="design_name" name="design_name" maxlength="64" placeholder="e.g. Adosia_Designs_123" type="text" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input update_is_free" type="radio" name="update_is_free" value="yes" id="update_free" checked>
                                <label class="form-check-label" for="update_free">
                                    <strong>Free</strong> Design
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input update_is_free" type="radio" name="update_is_free" value="no" id="update_paid">
                                <label class="form-check-label" for="update_paid">
                                    <strong>Paid</strong> Design
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="update_print_price_container" style="display: none;">
                            <label for="update_print_price" class="form-label"><strong>Print Price</strong></label>
                            <input id="update_print_price" name="update_print_price" aria-describedby="updatePrintPriceHelp" maxlength="10" placeholder="e.g. 15" type="number" min="1" class="form-control form-control-sm">
                            <div id="updatePrintPriceHelp" class="form-text">How much would you like to be paid in ₳DA, everytime this design is printed?</div>
                        </div>
                        <button type="submit" class="btn update-button btn-primary">
                            Designer: <strong>Update Price</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">4. [ Customer ] Print Purchase Order from Design Demo</h4>
                    <form id="print-form">
                        <div class="mb-3">
                            <label for="print_design_name" class="form-label"><strong>Adosia Design Name</strong></label>
                            <input id="print_design_name" name="print_design_name" maxlength="64" placeholder="e.g. Adosia_Designs_69" type="text" class="form-control form-control-sm" required>
                        </div>

                        <button type="submit" class="btn print-button btn-primary">
                            Customer: <strong>Print Design</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">5. [ Customer ] Remove Design Purchase Order Demo</h4>
                    <form id="remove-form">
                        <div class="mb-3">
                            <label for="remove_po_name" class="form-label"><strong>Adosia Design Purchase Order Name</strong></label>
                            <input id="remove_po_name" name="remove_po_name" maxlength="64" placeholder="e.g. Adosia_Designs_41_7" type="text" class="form-control form-control-sm" required>
                        </div>

                        <button type="submit" class="btn remove-button btn-primary">
                            Customer: <strong>Remove Design Purchase Order</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">6. [ Customer ] Add Design Purchase Order back into Printing Pool Demo</h4>
                    <form id="add-form">
                        <div class="mb-3">
                            <label for="add_po_name" class="form-label"><strong>Adosia Design Purchase Order Name</strong></label>
                            <input id="add_po_name" name="add_po_name" maxlength="64" placeholder="e.g. Adosia_Designs_41_7" type="text" class="form-control form-control-sm" required>
                        </div>

                        <button type="submit" class="btn add-button btn-primary">
                            Customer: <strong>Add Design Purchase Order</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">7. [ Printer Operator ] Make an Offer Demo</h4>
                    <form id="offer-form">
                        <div class="mb-3">
                            <label for="offer_po_name" class="form-label"><strong>Adosia Design Purchase Order Name</strong></label>
                            <input id="offer_po_name" name="offer_po_name" maxlength="64" placeholder="e.g. Adosia_Designs_33_2" type="text" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label for="offer_ada_amount" class="form-label"><strong>Offer Amount in ₳DA</strong></label>
                            <input id="offer_ada_amount" name="offer_ada_amount" aria-describedby="offerADAAmountHelp" type="number" min="1" step="1" class="form-control form-control-sm" required>
                            <div id="offerADAAmountHelp" class="form-text">How much would you charge in ₳DA to print this?</div>
                        </div>
                        <div class="mb-3">
                            <label for="offer_delivery_date" class="form-label"><strong>Estimated Delivery Date</strong></label>
                            <input id="offer_delivery_date" name="offer_delivery_date" aria-describedby="deliveryDateHelp" type="date" placeholder="yyyy-mm-dd" min="2023-01-01" max="2024-01-01" class="form-control form-control-sm" required>
                            <div id="deliveryDateHelp" class="form-text">Estimate how long you will take to complete this job</div>
                        </div>
                        <button type="submit" class="btn offer-button btn-primary">
                            Printer Operator: <strong>Make an Offer</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">8. [ Printer Operator ] Remove an Offer Demo</h4>
                    <form id="remove-offer-form">
                        <div class="mb-3">
                            <label for="remove_offer_utxo" class="form-label"><strong>Offer UTXO</strong></label>
                            <input id="remove_offer_utxo" name="remove_offer_utxo" placeholder="e.g. 9cbbbcd1f5e8d0f5843c0106ff34a8eed25c141141be9c0a08291d7b1e7b61e9#0" type="text" class="form-control form-control-sm" required>
                        </div>
                        <button type="submit" class="btn remove-offer-button btn-primary">
                            Printer Operator: <strong>Remove an Offer</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">9. [ Customer ] Accept an Offer Demo</h4>
                    <form id="accept-offer-form">
                        <div class="mb-3">
                            <label for="accept_po_utxo" class="form-label"><strong>PO UTXO</strong></label>
                            <input id="accept_po_utxo" name="accept_po_utxo" placeholder="e.g. f503ee522a0844b9b46fc087de3601a43c4b9d45c96a79fb01142e69048fd496#1" type="text" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label for="accept_offer_utxo" class="form-label"><strong>Offer UTXO</strong></label>
                            <input id="accept_offer_utxo" name="accept_offer_utxo" placeholder="e.g. 9cbbbcd1f5e8d0f5843c0106ff34a8eed25c141141be9c0a08291d7b1e7b61e9#0" type="text" class="form-control form-control-sm" required>
                        </div>
                        <button type="submit" class="btn accept-offer-button btn-primary">
                            Customer: <strong>Accept an Offer</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">10. [ Printer Operator ] Set job as Shipped Demo</h4>
                    <form id="set-shipped-form">
                        <div class="mb-3">
                            <label for="shipped_po_utxo" class="form-label"><strong>PO UTXO</strong></label>
                            <input id="shipped_po_utxo" name="shipped_po_utxo" placeholder="e.g. 66747a201f6de0b4a9afc9acb1f6e46b7aea0c27e9f494fb5de2e05a526b62f8#0" type="text" class="form-control form-control-sm" required>
                        </div>
                        <button type="submit" class="btn set-shipped-button btn-primary">
                            Printer Operator: <strong>Accept an Offer</strong>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">11. [ Customer ] Accept Shipment Demo</h4>
                    <form id="accept-shipment-form">
                        <div class="mb-3">
                            <label for="accept_shipment_po_utxo" class="form-label"><strong>PO UTXO</strong></label>
                            <input id="accept_shipment_po_utxo" name="accept_shipment_po_utxo" placeholder="e.g. f8d989bc22f4709f914075182b635baa3b8cad86c8a2502b61d8883d2d5d238f#0" type="text" class="form-control form-control-sm" required>
                        </div>
                        <button type="submit" class="btn accept-shipment-button btn-primary">
                            Customer: <strong>Accept Shipment</strong>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.2/js/bootstrap.bundle.min.js" integrity="sha512-BOsvKbLb0dB1IVplOL9ptU1EYA+LuCKEluZWRUYG73hxqNBU85JBIBhPGwhQl7O633KtkjMv8lvxZcWP+N3V3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.6.2/sweetalert2.min.js" integrity="sha512-rK2AiUjuQZfFvsW3A2pKQSPepwN2KI1U3m+oNQsmsQQ5nutlUaKCv2+H1oJpVJjNuMklUmTQkoGaIVz5kpBzjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@stricahq/cbors@1.0.2/dist/index.min.js"></script>
    <script type="text/javascript" src="https://cdn.dripdropz.io/wallet-connector/csl-v10.0.4/bundle.js"></script>
    <script type="module">
        import { C as CSL } from 'https://cdn.jsdelivr.net/npm/lucid-cardano@0.8.8/web/mod.js';
        (async function ($) {

            const networkMode = {{ isTestnet() ? 0 : 1 }};
            const targetNetwork = '{{ ucwords(env('CARDANO_NETWORK')) }}';

            const supportedWallets = [
                'eternl',
                'flint',
                'typhoncip30',
                'gerowallet',
                'LodeWallet',
                'nufi',
                'nami',
            ];

            const supportedWalletNames = {
                eternl: 'Eternl',
                flint: 'Flint',
                typhoncip30: 'Typhon',
                gerowallet: 'Gero',
                LodeWallet: 'Lode',
                nufi: 'NuFi',
                nami: 'Nami',
            };

            window.connectedWallet = undefined;

            const showToast = (type, message, timer = false) => Swal.fire({
                icon: type,
                html: message,
                timer,
                showConfirmButton: (timer === false),
            });

            const fromHex = (hex) => {
                return Buffer.Buffer.from(hex, "hex");
            };

            const toHex = (bytes) => {
                return Buffer.Buffer.from(bytes).toString('hex');
            };

            const stringToHex = (string) => {
                return new Buffer.Buffer(string).toString('hex');
            };

            const token = (amount, policyId, assetName) => {
                return {
                    pid: policyId,
                    tkn: assetName,
                    amt: parseInt(amount),
                };
            };

            const utxoCborToJSON = (cborString) => {
                let jsonObj = {}
                const data = cbors.Decoder.decode(fromHex(cborString)).value;
                const txInfo = data[0];
                jsonObj.txId = toHex(txInfo[0]);
                jsonObj.index = parseInt(txInfo[1]);
                jsonObj.tokens = [];
                const valueInfo = data[1];
                if (typeof(valueInfo[1]) === 'number') {
                    jsonObj.pkh = toHex(valueInfo[0]);
                    jsonObj.minAda = parseInt(valueInfo[1]);
                } else {
                    jsonObj.pkh = toHex(valueInfo[0]);
                    jsonObj.minAda = parseInt(valueInfo[1][0]);
                    const allTokens = valueInfo[1][1];
                    for (const [pid, token] of allTokens) {
                        const thnPid = toHex(pid);
                        for (const [tknItem, value] of token) {
                            jsonObj.tokens.push({
                                pid: thnPid,
                                tkn: toHex(tknItem),
                                amt: value,
                            });
                        }
                    }
                }
                return jsonObj;
            };

            let valueToCbor = (lovelace, valueObj) => {
                const valueMap = new Map()
                for (const pid in valueObj) {
                    for (const tkn in valueObj[pid]) {
                        const tokenMap = new Map();
                        tokenMap.set(fromHex(tkn), valueObj[pid][tkn]);
                        valueMap.set(fromHex(pid), tokenMap);
                    }
                }
                return cbors.Encoder.encode([lovelace, valueMap]).toString('hex');
            };

            const getNested = (obj, ...args) => {
                return args.reduce((obj, level) => obj && obj[level], obj)
            };

            const parseInputsAndChange = (allInputs, selectedAssets) => {
                const usedInputs = [];
                const returnedAssets = [];

                let allInputsFlat = {};
                allInputs.forEach((input) => {
                    usedInputs.push(input.utxo);
                    input.tokens.forEach((token) => {
                        if (getNested(allInputsFlat, token.pid) === undefined) {
                            allInputsFlat[token.pid] = {};
                        }
                        if (getNested(allInputsFlat, token.pid, token.tkn) === undefined) {
                            allInputsFlat[token.pid][token.tkn] = 0;
                        }
                        allInputsFlat[token.pid][token.tkn] += token.amt;
                    });
                });

                selectedAssets.forEach((selectedAsset) => {
                    allInputsFlat[selectedAsset.pid][selectedAsset.tkn] -= selectedAsset.amt;
                });

                for (const pid in allInputsFlat) {
                    for (const tkn in allInputsFlat[pid]) {
                        const remaining = allInputsFlat[pid][tkn];
                        if (remaining > 0) {
                            returnedAssets.push({
                                pid: pid,
                                tkn: tkn,
                                amt: remaining,
                            });
                        }
                    }
                }

                return {
                    usedInputs,
                    returnedAssets,
                };
            };

            const getAllInputs = (utxosCborList) => {
                let inputs = []
                utxosCborList.forEach(utxo => {
                    const data = utxoCborToJSON(utxo)
                    let tokens = []
                    data.tokens.forEach(tkn => {
                        tokens.push(token(tkn.amt,tkn.pid,tkn.tkn));
                    });
                    inputs.push({
                        utxo: data.txId + '#' +  data.index,
                        lovelace: data.minAda,
                        tokens,
                    });
                });
                return inputs;
            };

            const getFundedUtxos = (allInputs, designerCollateral) => {
                let utxoList = [];
                allInputs.forEach(input => {
                    if (input.tokens.length === 0 && input.utxo !== designerCollateral) {
                        utxoList.push(input.utxo);
                    }
                });
                return utxoList;
            };

            const $walletIntegration = $('div#wallet-integration');
            const $demoActions = $('div#demo-actions');

            async function run() {
                $(document).ready(async function () {

                    const enableConnectWallet = () => {
                        $('button.connect-wallet')
                            .removeClass('disabled')
                            .removeClass('btn-primary')
                            .addClass('btn-outline-primary');
                    };

                    const disableConnectWallet = () => {
                        $('button.connect-wallet')
                            .addClass('disabled');
                    };

                    let walletIntegrationTimer = setInterval(function () {
                        if (!window.cardano) return;
                        let loadedWalletCount = 0;
                        $walletIntegration.html('');
                        supportedWallets.forEach(function (supportedWalletName) {
                            if (window.cardano[supportedWalletName] !== undefined) {
                                $walletIntegration.append(`
                                    <button data-wallet="${ supportedWalletName }" title="Connec ${ supportedWalletNames[supportedWalletName] } Wallet" class="connect-wallet btn btn-outline-primary">
                                        <img width="32" height="32" src="${ window.cardano[supportedWalletName].icon }" alt="">
                                    </button>
                                `);
                                loadedWalletCount++;
                            }
                        });
                        if (loadedWalletCount === 0) {
                            $walletIntegration.html('Cardano Lite Wallets Not Detected');
                        }
                        clearInterval(walletIntegrationTimer);
                    }, 500);

                    $walletIntegration.on('click', 'button.connect-wallet', async function () {

                        $(this).removeClass('btn-outline-primary').addClass('btn-primary');

                        window.walletName = $(this).data('wallet');
                        if (!walletName || window.cardano[walletName] === undefined) {
                            showToast('error', 'Invalid connect wallet request');
                            return;
                        }

                        disableConnectWallet();

                        if (window.connectedWallet === undefined) {
                            try {
                                window.connectedWallet = await window.cardano[walletName].enable();
                                if (window.connectedWallet === undefined) {
                                    throw Error('Not Connected');
                                }
                            } catch (err) {
                                showToast('error', `Could not connect to <strong>${ supportedWalletNames[walletName] }</strong> wallet<hr>${ err.info || 'Wallet connection rejected by user' }`);
                                enableConnectWallet();
                                return;
                            }
                        }

                        if (window.connectedWallet.getNetworkId === undefined) {
                            showToast('error', 'Could not determine cardano network or wallet not connected');
                            enableConnectWallet();
                            return;
                        }

                        const walletNetwork = await window.connectedWallet.getNetworkId();
                        if (walletNetwork !== networkMode) {
                            showToast('error', `Wrong cardano network<br>Please switch to <strong>${ targetNetwork }</strong> network`);
                            enableConnectWallet();
                            return;
                        }

                        showToast('success', `Connected to <strong>${ supportedWalletNames[walletName] }</strong> wallet`, 1000);

                        $demoActions.show();

                    });

                    $demoActions.on('change', 'input.is_free', function () {
                        if ($(this).val() === 'no') {
                            $('div#print_price_container').show();
                            $('input#print_price').attr('required', true);
                        } else {
                            $('div#print_price_container').hide();
                            $('input#print_price').removeAttr('required');
                        }
                    });

                    $demoActions.on('change', 'input.update_is_free', function () {
                        if ($(this).val() === 'no') {
                            $('div#update_print_price_container').show();
                            $('input#update_print_price').attr('required', true);
                        } else {
                            $('div#update_print_price_container').hide();
                            $('input#update_print_price').removeAttr('required');
                        }
                    });

                    $demoActions.on('submit', 'form#mint-form', async function (e) {

                        e.preventDefault();

                        const mintData = $('form#mint-form').serializeArray();
                        $('form#mint-form input').attr('disabled', true);
                        $('button.mint-button').addClass('disabled');

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const designerCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), designerCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const designerPKH = walletBasePKH.slice(2, 58);
                        const designerStakeKey = walletBasePKH.slice(58);
                        const designerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const mintRequest = {};
                        mintData.forEach((mintDatum) => {
                            if (mintDatum.name.indexOf('stl_files') === -1 &&
                                mintDatum.name.indexOf('print_quantities') === -1 &&
                                mintDatum.name.indexOf('is_free') === -1 &&
                                mintDatum.name.indexOf('print_price') === -1
                            ) {
                                mintRequest[mintDatum.name] = mintDatum.value.trim();
                            }
                        });
                        if ($('input#paid').is(':checked')) {
                            const printPrice = mintData.find(s => s.name === 'print_price');
                            mintRequest.print_price_lovelace = parseInt(printPrice.value) * 1000000;
                        } else {
                            mintRequest.print_price_lovelace = 1;
                        }
                        mintRequest.stl_models = [];
                        for (let i = 1; i <= 3; i++) {
                            const stlFile = mintData.find(s => s.name === 'stl_files_' + i);
                            const printQuantity = mintData.find(s => s.name === 'print_quantities_' + i);
                            if (stlFile.value.length && printQuantity.value.length) {
                                mintRequest.stl_models.push({
                                    qty: parseInt(printQuantity.value),
                                    src: stlFile.value.trim(),
                                });
                            }
                        }
                        mintRequest.designer_pkh = designerPKH;
                        mintRequest.designer_stake_key = designerStakeKey;
                        mintRequest.designer_change_address = designerChangeAddress;
                        mintRequest.designer_input_tx_ids = fundedUtxos;
                        mintRequest.designer_collateral = designerCollateral;

                        const settings = {
                            "url": "/designer/mint/design",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(mintRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                transactionWitnessSet.set_redeemers(txWitness.redeemers());

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#update-form', async function(e) {

                        e.preventDefault();

                        $('form#update-form input').attr('disabled', true);
                        $('button.update-button').addClass('disabled');

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const designerCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), designerCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const designerPKH = walletBasePKH.slice(2, 58);
                        const designerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const designName = $('input#design_name').val();
                        const isFree = $('input#update_free').is(':checked');
                        const newPrice = $('input#update_print_price').val();

                        const updateRequest = {
                            design_name: designName,
                            is_free: isFree,
                            print_price_lovelace: (isFree ? 1 : (parseInt(newPrice) * 1000000)),
                            designer_pkh: designerPKH,
                            designer_change_address: designerChangeAddress,
                            designer_input_tx_ids: fundedUtxos,
                            designer_collateral: designerCollateral,
                        };

                        const settings = {
                            "url": "/designer/mint/update",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(updateRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                transactionWitnessSet.set_redeemers(txWitness.redeemers());

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#print-form', async function(e) {

                        e.preventDefault();

                        $('form#print-form input').attr('disabled', true);
                        $('button.print-button').addClass('disabled');

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const customerCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), customerCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const customerPKH = walletBasePKH.slice(2, 58);
                        const customerStakeKey = walletBasePKH.slice(58);
                        const customerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const designName = $('input#print_design_name').val();

                        const printRequest = {
                            design_name: designName,
                            customer_pkh: customerPKH,
                            customer_stake_key: customerStakeKey,
                            customer_change_address: customerChangeAddress,
                            customer_input_tx_ids: fundedUtxos,
                            customer_collateral: customerCollateral,
                        };

                        const settings = {
                            "url": "/customer/purchase-order/print-design",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(printRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                transactionWitnessSet.set_redeemers(txWitness.redeemers());

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#remove-form', async function(e) {

                        e.preventDefault();

                        $('form#remove-form input').attr('disabled', true);
                        $('button.remove-button').addClass('disabled');

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const customerCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), customerCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const customerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const poName = $('input#remove_po_name').val();

                        const removeRequest = {
                            po_name: poName,
                            customer_change_address: customerChangeAddress,
                            customer_input_tx_ids: fundedUtxos,
                            customer_collateral: customerCollateral,
                        };

                        const settings = {
                            "url": "/customer/purchase-order/remove",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(removeRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                transactionWitnessSet.set_redeemers(txWitness.redeemers());

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#add-form', async function(e) {

                        e.preventDefault();

                        $('form#add-form input').attr('disabled', true);
                        $('button.add-button').addClass('disabled');

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const customerPKH = walletBasePKH.slice(2, 58);
                        const customerStakeKey = walletBasePKH.slice(58);
                        const customerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const poName = $('input#add_po_name').val();

                        const selectedAsset = {
                            "pid": "{{ env('PURCHASE_ORDER_POLICY_ID') }}",
                            "tkn": stringToHex(poName),
                            "amt": 1,
                        };

                        let allInputs;
                        if (window.walletName === 'nami') {
                            allInputs = getAllInputs(await window.connectedWallet.getUtxos());
                        } else {
                            const valueObj = {}
                            valueObj[selectedAsset.pid] = {}
                            valueObj[selectedAsset.pid][selectedAsset.tkn] = selectedAsset.amt;
                            const cborOutput = valueToCbor(10000000, valueObj);
                            allInputs = getAllInputs(await window.connectedWallet.getUtxos(cborOutput));
                        }

                        const inputsAndChange = parseInputsAndChange(allInputs, [selectedAsset]);

                        const addRequest = {
                            po_name: poName,
                            customer_pkh: customerPKH,
                            customer_stake_key: customerStakeKey,
                            customer_change_address: customerChangeAddress,
                            customer_input_tx_ids: inputsAndChange.usedInputs,
                            customer_returned_assets: inputsAndChange.returnedAssets,
                        };

                        const settings = {
                            "url": "/customer/purchase-order/add",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(addRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                // TODO: Notice me senpai this needs to be in the global tx handler
                                // TODO: not everything will have redeemers for e.g.
                                if (txWitness.redeemers() !== undefined) {
                                    transactionWitnessSet.set_redeemers(txWitness.redeemers());
                                }

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#offer-form', async function(e) {

                        e.preventDefault();

                        $('form#offer-form input').attr('disabled', true);
                        $('button.offer-button').addClass('disabled');

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const printerOperatorPKH = walletBasePKH.slice(2, 58);
                        const printerOperatorStakeKey = walletBasePKH.slice(58);
                        const printerOperatorChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const printerOperatorCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), printerOperatorCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const poName = $('input#offer_po_name').val();
                        const offerAmount = parseInt($('input#offer_ada_amount').val()) * 1000000;
                        const deliveryDate = $('input#offer_delivery_date').val();

                        const offerRequest = {
                            po_name: poName,
                            offer_amount: offerAmount,
                            delivery_date: deliveryDate,
                            printer_operator_pkh: printerOperatorPKH,
                            printer_operator_stake_key: printerOperatorStakeKey,
                            printer_operator_change_address: printerOperatorChangeAddress,
                            printer_operator_input_tx_ids: fundedUtxos,
                        };

                        const settings = {
                            "url": "/printer-operator/purchase-order/make-offer",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(offerRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                // TODO: Notice me senpai this needs to be in the global tx handler
                                // TODO: not everything will have redeemers for e.g.
                                if (txWitness.redeemers() !== undefined) {
                                    transactionWitnessSet.set_redeemers(txWitness.redeemers());
                                }

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#remove-offer-form', async function(e) {

                        e.preventDefault();

                        $('form#remove-offer-form input').attr('disabled', true);
                        $('button.remove-offer-button').addClass('disabled');

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const printerOperatorPKH = walletBasePKH.slice(2, 58);
                        const printerOperatorStakeKey = walletBasePKH.slice(58);
                        const printerOperatorChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const printerOperatorCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), printerOperatorCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const offerUTXO = $('input#remove_offer_utxo').val();

                        const removeOfferRequest = {
                            offer_utxo: offerUTXO,
                            printer_operator_pkh: printerOperatorPKH,
                            printer_operator_stake_key: printerOperatorStakeKey,
                            printer_operator_change_address: printerOperatorChangeAddress,
                            printer_operator_input_tx_ids: fundedUtxos,
                            printer_operator_collateral: printerOperatorCollateral,
                        };

                        const settings = {
                            "url": "/printer-operator/purchase-order/remove-offer",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(removeOfferRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                // TODO: Notice me senpai this needs to be in the global tx handler
                                // TODO: not everything will have redeemers for e.g.
                                if (txWitness.redeemers() !== undefined) {
                                    transactionWitnessSet.set_redeemers(txWitness.redeemers());
                                }

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#accept-offer-form', async function(e) {

                        e.preventDefault();

                        $('form#accept-offer-form input').attr('disabled', true);
                        $('button.accept-offer-button').addClass('disabled');

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const customerPKH = walletBasePKH.slice(2, 58);
                        const customerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const customerCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), customerCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const poUTXO = $('input#accept_po_utxo').val();
                        const offerUTXO = $('input#accept_offer_utxo').val();

                        const acceptOfferRequest = {
                            po_utxo: poUTXO,
                            offer_utxo: offerUTXO,
                            customer_pkh: customerPKH,
                            customer_change_address: customerChangeAddress,
                            customer_input_tx_ids: fundedUtxos,
                            customer_collateral: customerCollateral,
                        };

                        const settings = {
                            "url": "/customer/purchase-order/accept-offer",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(acceptOfferRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                // TODO: Notice me senpai this needs to be in the global tx handler
                                // TODO: not everything will have redeemers for e.g.
                                if (txWitness.redeemers() !== undefined) {
                                    transactionWitnessSet.set_redeemers(txWitness.redeemers());
                                }

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#set-shipped-form', async function(e) {

                        e.preventDefault();

                        $('form#set-shipped-form input').attr('disabled', true);
                        $('button.set-shipped-button').addClass('disabled');

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const printerOperatorPKH = walletBasePKH.slice(2, 58);
                        const printerOperatorChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const printerOperatorCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), printerOperatorCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const poUTXO = $('input#shipped_po_utxo').val();

                        const setShippedRequest = {
                            po_utxo: poUTXO,
                            printer_operator_pkh: printerOperatorPKH,
                            printer_operator_change_address: printerOperatorChangeAddress,
                            printer_operator_input_tx_ids: fundedUtxos,
                            printer_operator_collateral: printerOperatorCollateral,
                        };

                        const settings = {
                            "url": "/printer-operator/purchase-order/set-shipped",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(setShippedRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                // TODO: Notice me senpai this needs to be in the global tx handler
                                // TODO: not everything will have redeemers for e.g.
                                if (txWitness.redeemers() !== undefined) {
                                    transactionWitnessSet.set_redeemers(txWitness.redeemers());
                                }

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                    $demoActions.on('submit', 'form#accept-shipment-form', async function(e) {

                        e.preventDefault();

                        $('form#accept-shipment-form input').attr('disabled', true);
                        $('button.accept-shipment-button').addClass('disabled');

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const customerPKH = walletBasePKH.slice(2, 58);
                        const customerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const getCollateral = window.connectedWallet.experimental.getCollateral || window.connectedWallet.getCollateral;
                        const collateralCBOR = await getCollateral();
                        if (collateralCBOR.length === 0) {
                            showToast('error', `Please configure <strong>Collateral</strong> in your wallet`);
                            return;
                        }
                        const collateralUtxo = utxoCborToJSON(collateralCBOR[0]);
                        const customerCollateral = `${ collateralUtxo.txId }#${ collateralUtxo.index }`;

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()), customerCollateral);
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        const poUTXO = $('input#accept_shipment_po_utxo').val();

                        const acceptShipmentRequest = {
                            po_utxo: poUTXO,
                            customer_pkh: customerPKH,
                            customer_change_address: customerChangeAddress,
                            customer_input_tx_ids: fundedUtxos,
                            customer_collateral: customerCollateral,
                        };

                        const settings = {
                            "url": "/customer/purchase-order/accept-shipment",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify(acceptShipmentRequest),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                const tx = CSL.Transaction.from_bytes(fromHex(response.data.transaction));
                                const txWitness = tx.witness_set();
                                const txMetadata = tx.auxiliary_data();

                                const txVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                const witnesses = CSL.TransactionWitnessSet.from_bytes(fromHex(txVkeyWitnesses));

                                const transactionWitnessSet = CSL.TransactionWitnessSet.new();
                                transactionWitnessSet.set_vkeys(witnesses.vkeys());
                                // TODO: Notice me senpai this needs to be in the global tx handler
                                // TODO: not everything will have redeemers for e.g.
                                if (txWitness.redeemers() !== undefined) {
                                    transactionWitnessSet.set_redeemers(txWitness.redeemers());
                                }

                                const signedTx = CSL.Transaction.new(
                                    tx.body(),
                                    transactionWitnessSet,
                                    txMetadata,
                                );

                                let singedTxCBOR = toHex(signedTx.to_bytes()).toLowerCase();
                                if (singedTxCBOR.indexOf('d90103a100') === -1) {
                                    singedTxCBOR = singedTxCBOR.replace('a11902d1', 'd90103a100a11902d1');
                                }

                                await window.connectedWallet.submitTx(singedTxCBOR);

                                // TODO: To calculate the real tx id, see: https://ddzgroup.slack.com/archives/D0494H6NT40/p1670462391398179
                                // TODO: Use the Constants to re-create signed tx file
                                showToast('success', `Transaction was <strong>success</strong>`);

                            } else {

                                showToast('error', response.error.message || response.error);

                            }
                        }).catch(err => {
                            showToast('error', 'Something went wrong, check developer console');
                            console.log(err);
                        });

                    });

                });
            }

            await run();

        })(jQuery);
    </script>

</body>
</html>
