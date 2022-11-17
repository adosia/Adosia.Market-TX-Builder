<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Latheesan Kanesamoorthy">
    <title>TX Builder Demo</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/sign-in/">
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
            <h4 class="mb-3">2. Run Demo</h4>
            <button class="btn btn-primary demo-action designer-mint">
                Designer: Mint &amp; Send to Marketplace
            </button>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.2/js/bootstrap.bundle.min.js" integrity="sha512-BOsvKbLb0dB1IVplOL9ptU1EYA+LuCKEluZWRUYG73hxqNBU85JBIBhPGwhQl7O633KtkjMv8lvxZcWP+N3V3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.6.2/sweetalert2.min.js" integrity="sha512-rK2AiUjuQZfFvsW3A2pKQSPepwN2KI1U3m+oNQsmsQQ5nutlUaKCv2+H1oJpVJjNuMklUmTQkoGaIVz5kpBzjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@stricahq/cbors@1.0.2/dist/index.min.js"></script>
    <script type="text/javascript" src="https://cdn.dripdropz.io/wallet-connector/csl-v10.0.4/bundle.js"></script>
    <script type="module">
        import { C as CSL } from 'https://unpkg.com/lucid-cardano@0.6.9/web/mod.js';
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

            const getFundedUtxos = (allInputs) => {
                let utxoList = [];
                allInputs.forEach(input => {
                    if (input.tokens.length === 0) {
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

                        const walletName = $(this).data('wallet');
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

                        showToast('success', `Connected to <strong>${ supportedWalletNames[walletName] }</strong> wallet`, 1500);

                        $demoActions.show();

                    });

                    $demoActions.on('click', 'button.designer-mint', async function () {

                        const fundedUtxos = getFundedUtxos(getAllInputs(await window.connectedWallet.getUtxos()));
                        if (fundedUtxos.length === 0) {
                            showToast('error', 'Pure ada only utxo inputs exhausted, send 5 ada to yourself and try again');
                            return;
                        }

                        $('button.demo-action').addClass('disabled');

                        const walletBasePKH = await window.connectedWallet.getChangeAddress();
                        const designerPKH = walletBasePKH.slice(2, 58);
                        const designerStakeKey = walletBasePKH.slice(58);
                        const designerChangeAddress = CSL.Address.from_bytes(Uint8Array.from(fromHex(walletBasePKH))).to_bech32(
                            'addr' + (networkMode === 0 ? '_test' : ''),
                        );

                        const settings = {
                            "url": "/mint/design",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify({
                                "thumbnail": "ipfs://QmQWG57Vpq2pPfuzBn2bS8UEj4M1GnCa5PpqSU6k5fyNQC",
                                "glb_model": "ipfs://QmQTXTycfwuEfr4Lk6W1Za8UjzyQor2znwVKfd7Jy7DUaM",
                                "stl_models": [
                                    {
                                        "model_source": "ifps://QmRyBaGYu1bbthdkHUA2YjyUK43uPtKcoVuF8NpFk2Z6tm",
                                        "print_quantity": 2
                                    },
                                    {
                                        "model_source": "ifps://QmZCX5tXTko3wzyttK63SNTMpgfoGLwzUj7i6hA6tEWfqy",
                                        "print_quantity": 1
                                    }
                                ],
                                "print_price_lovelace": 10000000,
                                "designer_pkh": designerPKH,
                                "designer_stake_key": designerStakeKey,
                                "designer_change_address": designerChangeAddress,
                                "designer_input_tx_ids": fundedUtxos,
                                "design_name_prefix": "SpaceRocket",
                            }),
                        };

                        $.ajax(settings).done(async function (response) {
                            if (response.data) {

                                let walletTxVkeyWitnesses = null;
                                try {
                                    walletTxVkeyWitnesses = await window.connectedWallet.signTx(response.data.transaction, true);
                                } catch (err) {
                                    console.error('SignTX', err);
                                    showToast('error', err.info || err);
                                    return;
                                }

                                console.log('transactionCbor', response.data.transaction);
                                console.log('walletTxVkeyWitnesses', walletTxVkeyWitnesses);

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
