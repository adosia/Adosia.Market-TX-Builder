<?php

use App\Exceptions\AppException;

/**
 * @throws Exception
 */
function createTempDir(string $context = null): string {
    $entropy = random_bytes(128);
    $tempDir = storage_path('temp/' . md5(uniqid($context . $entropy . microtime(true), true)));
    $tempDirCreated = @mkdir($tempDir, 0700, true);
    if ($tempDirCreated !== true) {
        throw new AppException('could not create temp directory');
    }
    return $tempDir;
}

/**
 * @param $dir
 */
function rrmdir($dir): void {
    if (is_dir($dir) && str_contains($dir, storage_path())) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object !== "." && $object !== "..") {
                if (is_dir($dir. DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                    rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        rmdir($dir);
    }
}

/**
 * @param string $tempDir
 */
function exportProtocolParams(string $tempDir): void {
    $command = sprintf(
        '%s query protocol-parameters %s --out-file %s',
        CARDANO_CLI,
        cardanoNetworkFlag(),
        "$tempDir/protocol.json"
    );
    shell_exec($command);
}

/**
 * @return string
 */
function cardanoNetworkFlag(): string {
    $cardanoNetwork = env('CARDANO_NETWORK');
    $testnetMagicNo = match ($cardanoNetwork) {
        'preview' => $testnetMagicNo = 2,
        'preprod' => $testnetMagicNo = 5,
        default => $testnetMagicNo = -1,
    };
    return $cardanoNetwork === 'mainnet'
        ? '--mainnet'
        : '--testnet-magic ' . $testnetMagicNo;
}

/**
 * @param string $value
 * @return string
 */
function toShortHash(string $value): string {
    return substr(base_convert(md5($value), 16,32), 0, 12);
}
