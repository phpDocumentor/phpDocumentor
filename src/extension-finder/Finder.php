<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use RuntimeException;

use function dirname;
use function escapeshellcmd;
use function file_exists;
use function file_put_contents;
use function getcwd;
use function in_array;
use function json_decode;
use function realpath;
use function rtrim;
use function shell_exec;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

use const PHP_BINARY;
use const PHP_EOL;

/**
 * This file is part of phpDocumentor.
 *
 * The application in this file is used to find all installed phpDocumentor extensions from the
 * current directory. We use this because the project consuming phpDocumentor may not be compatible with
 * the libraries used in phpDocumentor. As phpDocumentor is using symfony, your project might use a different
 * version. Autoloading might dangerous for the execution of phpDocumentor.
 */
class Finder
{
    public static function findProjectRoot(string $startDir): string|null
    {
        $dir = realpath($startDir);
        while ($dir && $dir !== dirname($dir)) {
            if (file_exists($dir . '/vendor/autoload.php')) {
                return $dir;
            }

            $dir = dirname($dir);
        }

        return null;
    }

    /** @return mixed[] */
    public static function getInstalledPackagesByType(string $projectDir, string $type = 'phpdoc-extension'): array
    {
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

        $command = escapeshellcmd($php . ' ' . $tmpFile);
        $output = shell_exec($command);
        unlink($tmpFile);

        if ($output === null) {
            throw new RuntimeException('Failed to execute subprocess');
        }

        return json_decode($output, true);
    }
}

$projectRoot = Finder::findProjectRoot(getcwd());
if (! $projectRoot) {
    if (in_array('-v', $argv)) {
        echo 'No project root found' . PHP_EOL;
    }

    return [];
}

return Finder::getInstalledPackagesByType($projectRoot);
