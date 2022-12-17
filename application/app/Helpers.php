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
 * @param string $command
 * @param string $function
 * @param string $file
 * @param int $line
 * @return string
 * @throws AppException
 */
function shellExec(string $command, string $function, string $file, int $line): string {
    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open($command, $descriptorSpec, $pipes, storage_path());
    if (is_resource($process)) {
        $stdOut = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stdErr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $retCode = proc_close($process);
        if ($retCode !== 0) {
            throw new AppException(sprintf(
                "Shell command failed - %s (%d) [ called by %s from file %s on line %d ]\nCommand: %s",
                $stdErr,
                $retCode,
                $function,
                basename($file),
                $line,
                $command,
            ));
        }
        if (empty($stdOut)) {
            $stdOut = 'EmptyResponse';
        }
        return trim($stdOut);
    }
    throw new AppException(sprintf(
        "Failed to open shell [ called by %s from file %s on line %d ]\nCommand: %s",
        $function,
        basename($file),
        $line,
        $command,
    ));
}

/**
 * @param string $tempDir
 * @throws AppException
 */
function exportProtocolParams(string $tempDir): void {
    $command = sprintf(
        '%s query protocol-parameters %s --out-file %s',
        CARDANO_CLI,
        cardanoNetworkFlag(),
        "$tempDir/protocol.json"
    );
    shellExec($command, __FUNCTION__, __FILE__, __LINE__);
}

/**
 * @return bool
 */
function isTestnet(): bool {
    return env('CARDANO_NETWORK') !== 'mainnet';
}

/**
 * @return int
 */
function cardanoNetworkMagic(): int {
    return match (env('CARDANO_NETWORK')) {
        'mainnet' => 0,
        'preview' => 2,
        'preprod' => 5,
        default => -1,
    };
}

/**
 * @return string
 */
function cardanoNetworkFlag(): string {
    return env('CARDANO_NETWORK') === 'mainnet'
        ? '--mainnet'
        : '--testnet-magic ' . cardanoNetworkMagic();
}

/**
 * @param string $value
 * @return string
 */
function toShortHash(string $value): string {
    return substr(base_convert(md5($value), 16,32), 0, 12);
}
