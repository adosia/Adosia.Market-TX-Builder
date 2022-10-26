'use strict';

const express = require('express');
const { Buffer } = require("buffer");
const S = require("@dcspark/cardano-multiplatform-lib-nodejs");

// Constants
const PORT = 80;
const HOST = '0.0.0.0';

// App
const app = express();
app.use(express.json());

// Root endpoint
app.get('/', (req, res) => {
    res.send('Hello World');
});

// Designer mint witness tx builder
app.post('/designer/mint/witness-tx', (req, res) => {
    const transaction = S.Transaction.from_bytes(Buffer.from(req.body.txBodyCbor, "hex"))

    const transaction_body = transaction.body();
    const txBodyHash = S.hash_transaction(transaction_body);

    const PrivateKey = S.PrivateKey.from_normal_bytes(Buffer.from(req.body.policySkeyCbor.substring(4), "hex"));

    const witness = S.make_vkey_witness(txBodyHash, PrivateKey);

    const witnessSet = S.TransactionWitnessSet.new();
    const vKeys = S.Vkeywitnesses.new();
    vKeys.add(witness);
    witnessSet.set_vkeys(vKeys);

    res.send(Buffer.from(witnessSet.to_bytes()).toString("hex"));
});

// Start server
app.listen(PORT, HOST, () => {
    console.log(`TX-Builder NodeJS Service Running on http://${HOST}:${PORT}`);
});
