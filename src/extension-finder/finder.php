<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * The application in this file is used to find all installed phpDocumentor extensions from the
 * current directory. We use this because the project consuming phpDocumentor may not be compatible with
 * the libraries used in phpDocumentor. As phpDocumentor is using symfony, your project might use a different
 * version. Autoloading might dangagrous for the execution of phpDocumentor.
 */

function findProjectRoot(string $startDir): ?string {
    $dir = realpath($startDir);
    while ($dir && $dir !== dirname($dir)) {
        if (file_exists($dir . '/vendor/autoload.php')) {
            return $dir;
        }
        $dir = dirname($dir);
    }
    return null;
}

function getInstalledPackagesByType(string $projectDir, string $type = 'phpdoc-extension'): array {
    $projectDir = rtrim($projectDir, '/');
    $php = PHP_BINARY;

    $script = <<<PHP
<?php
require_once '$projectDir/vendor/autoload.php';
use Composer\\InstalledVersions;

echo json_encode(
    array_map(
      fn(\$package) => InstalledVersions::getInstallPath(\$package),
      InstalledVersions::getInstalledPackagesByType('$type')
    ),
    JSON_PRETTY_PRINT
);
PHP;

    $tmpFile = tempnam(sys_get_temp_dir(), 'composer_inspect_') . '.php';
    file_put_contents($tmpFile, $script);

    $command = escapeshellcmd("$php $tmpFile");
    $output = shell_exec($command);
    unlink($tmpFile);

    if ($output === null) {
        throw new RuntimeException("Failed to execute subprocess");
    }

    return json_decode($output, true);
}

$projectRoot = findProjectRoot(getcwd());
if (!$projectRoot) {
    die("Could not find project with vendor/autoload.php\n");
}

return getInstalledPackagesByType($projectRoot);
