<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor;

use Composer\Autoload\ClassLoader;
use RuntimeException;

use function file_exists;
use function file_get_contents;
use function getenv;
use function json_decode;

final class AutoloaderLocator
{
    public static function autoload(): ClassLoader
    {
        return require self::findVendorPath() . '/autoload.php';
    }

    /**
     * Attempts to find the location of the vendor folder.
     *
     * This method tries to check for a autoload.php in a directory 4 levels above the folder of this Bootstrap file.
     * This is the expected location if phpDocumentor is installed using composer because the current directory for
     * this file is expected to be 'vendor/phpdocumentor/phpdocumentor/src/phpDocumentor'. This approach will work
     * independently from the name of the vendor directory.
     *
     * If not found, it will get the value of a
     * {@link https://getcomposer.org/doc/03-cli.md#composer-vendor-dir COMPOSER_VENDOR_DIR environment variable}
     * and use it as vendor directory name if not empty.
     *
     * If it's not specified, it will check if it is a standalone install (e.g. via git) and will look for a
     * composer.json file 2 levels above as we are supposed to be in 'src/phpDocumentor' (The configuration file
     *  can be named differently based on the
     * {@link https://getcomposer.org/doc/03-cli.md#composer COMPOSER environment variable}). If this file
     * contains a {@link https://getcomposer.org/doc/06-config.md#vendor-dir vendor-dir entry}, its value will be
     * used for the vendor directory location.
     *
     * If none of these has a specified value, it will use the default 'vendor' directory name.
     *
     * Finally, if the directory doesn't exist, it will throw an exception.
     *
     * @param  string $baseDir parameter for test purposes only.
     *
     * @return string The vendor directory path
     *
     * @throws RuntimeException If the vendor directory is not findable.
     */
    public static function findVendorPath(string $baseDir = __DIR__): string
    {
        // Composerised installation, vendor/phpdocumentor/phpdocumentor/src/phpDocumentor is __DIR__
        $vendorFolderWhenInstalledWithComposer = $baseDir . '/../../../../';
        if (file_exists($vendorFolderWhenInstalledWithComposer . '/autoload.php')) {
            $vendorDir = $vendorFolderWhenInstalledWithComposer;
        } else {
            // Repository cloned via git
            $vendorDir = $baseDir . '/../../' . self::getCustomVendorPathFromComposer(
                $baseDir . '/../../' . self::findComposerConfigurationPath()
            );
        }

        // Do not use realpath() here to don't break installation from phar
        if (!file_exists($vendorDir)) {
            throw new RuntimeException('Unable to find vendor directory for ' . $baseDir);
        }

        return $vendorDir;
    }

    /**
     * Retrieves the custom composer configuration path based on the
     * {@link https://getcomposer.org/doc/03-cli.md#composer COMPOSER environment variable}
     * or returns the default 'composer.json'.
     */
    public static function findComposerConfigurationPath(): string
    {
        $filename = getenv('COMPOSER') ?: 'composer';

        return $filename . '.json';
    }

    /**
     * Retrieves the custom vendor directory name from
     * the {@link https://getcomposer.org/doc/03-cli.md#composer-vendor-dir COMPOSER_VENDOR_DIR environment variable},
     * from the {@link https://getcomposer.org/doc/06-config.md#vendor-dir vendor-dir entry} of the given composer.json,
     * or returns 'vendor'.
     *
     * @param string $composerConfigurationPath the path pointing to the composer.json
     */
    private static function getCustomVendorPathFromComposer(string $composerConfigurationPath): string
    {
        $vendorDir = getenv('COMPOSER_VENDOR_DIR');
        if ($vendorDir) {
            return $vendorDir;
        }

        $vendorDir = 'vendor';
        if (file_exists($composerConfigurationPath)) {
            $composerFile = file_get_contents($composerConfigurationPath);
            $composerJson = json_decode($composerFile, true);
            if ($composerJson && !empty($composerJson['config']['vendor-dir'])) {
                $vendorDir = $composerJson['config']['vendor-dir'];
            }
        }

        return $vendorDir;
    }
}
