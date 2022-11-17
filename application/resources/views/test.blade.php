<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Latheesan Kanesamoorthy">
    <title>TX Builder Test</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/sign-in/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.2/css/bootstrap.min.css" integrity="sha512-CpIKUSyh9QX2+zSdfGP+eWLx23C8Dj9/XmHjZY2uDtfkdLGo0uY12jgcnkX9vXOgYajEKb/jiw67EYm+kBf+6g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.6.2/sweetalert2.min.css" integrity="sha512-5aabpGaXyIfdaHgByM7ZCtgSoqg51OAt8XWR2FHr/wZpTCea7ByokXbMX2WSvosioKvCfAGDQLlGDzuU6Nm37Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="p-3">

    <div class="container-fluid">

        <h4 class="mb-3">1. Connect Wallet <a href="/test" class="btn btn-primary btn-sm">Change Wallet</a></h4>
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
    <script type="module">
        import * as mesh from 'http://localhost:8888/cardano-mesh/mesh.cjs';
        (async function ($) {

            const networkMode = {{ isTestnet() ? 0 : 1 }};
            const targetNetwork = '{{ ucwords(env('CARDANO_NETWORK')) }}';
            const supportedWallets = {
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

            const $walletIntegration = $('div#wallet-integration');
            const $demoActions = $('div#demo-actions');

            async function run() {
                $(document).ready(async function () {

                    let walletIntegrationTimer = setInterval(function () {
                        if (!window.cardano) return;
                        let loadedWalletCount = 0;
                        $walletIntegration.html('');
                        supportedWallets.forEach(function (walletName, walletId) {
                            if (window.cardano[walletId] !== undefined) {
                                $walletIntegration.append(`
                                    <button data-wallet="${ walletId }" title="Connec ${ walletName } Wallet" class="connect-wallet btn btn-outline-primary">
                                        <img width="32" height="32" src="${ window.cardano[walletId].icon }" alt="">
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

                        const walletId = $(this).data('wallet');
                        if (!walletId || window.cardano[walletId] === undefined) {
                            showToast('error', 'Invalid connect wallet request');
                            return;
                        }

                        disableConnectWallet();

                        try {
                            window.connectedWallet = mesh.BrowserWallet.enable(walletId);
                        } catch (e) {
                            showToast('error', 'Failed to connect to wallet');
                            enableConnectWallet();
                            return;
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

                        showToast('success', `Connected to <strong>${ supportedWallets[walletId] }</strong> wallet`, 1500);

                        $demoActions.show();

                    });

                });
            }

            await run();

        })(jQuery);
    </script>

</body>
</html>
