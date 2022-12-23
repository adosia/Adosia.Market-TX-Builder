const EMS = require('@emurgo/cardano-message-signing-nodejs');
const CSL = require('@emurgo/cardano-serialization-lib-nodejs');
const Buffer = require('buffer');
const cbor = require('cbor');

const toHexBuffer = (hex) => Buffer.Buffer.from(hex, 'hex');
const toHexString = (array) => Buffer.Buffer.from(array).toString('hex');

const sigKeyToPublicKey = (sig_key) => {
    const decoded = cbor.decode(sig_key);
    return CSL.PublicKey.from_bytes(toHexBuffer(decoded.get(-2)));
};

const publicKeyToStakeKey = (publicKey, isTestnet) => {
    const stake_arg = `e${ isTestnet ? 0 : 1 }` + toHexString(publicKey.hash('hex').to_bytes());
    return CSL.Address.from_bytes(toHexBuffer(stake_arg));
};

const authValidateSignature = (jsonPayload) => {
    try {
        const payload = JSON.parse(jsonPayload);
        const publicKey = sigKeyToPublicKey(payload.signature_key);
        const stakeAddr = publicKeyToStakeKey(publicKey, payload.is_testnet);
        const coseSign1 = EMS.COSESign1.from_bytes(toHexBuffer(payload.signature_cbor));
        const signedData = coseSign1.signed_data();
        const sig = CSL.Ed25519Signature.from_bytes(coseSign1.signature());

        const walletMatches = stakeAddr.to_bech32('stake' + (payload.is_testnet ? '_test' : '')) === payload.stake_key;
        const signatureValidates = publicKey.verify(signedData.to_bytes(), sig);
        const payloadMatches = toHexString(signedData.payload()) === payload.challenge;

        return (
            walletMatches &&
            signatureValidates &&
            payloadMatches
        );
    } catch (e) {
        return false;
    }
};

console.log(authValidateSignature(process.argv.slice(2)[0]));
