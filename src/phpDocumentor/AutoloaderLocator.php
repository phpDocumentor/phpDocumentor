<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 *
 *
 */

namespace phpDocumentor;

final class AutoloaderLocator
{
    /**
     * @codeCoverageIgnore cannot test without side-effects
     */
    public static function autoload()
    {
        return require static::findVendorPath(). '/autoload.php';
    }

    /**
     * Attempts to find the location of the vendor folder.
     *
     * This method tries to check for a composer.json in a directory 5 levels below the folder of this Bootstrap file.
     * This is the expected location if phpDocumentor is installed using composer because the current directory for
     * this file is expected to be 'vendor/phpdocumentor/phpdocumentor/src/phpDocumentor'.
     *
     * If a composer.json is found we will try to extract the vendor folder name using the 'vendor-dir' configuration
     * option of composer or assume it is vendor if that option is not set.
     *
     *
     * If no custom composer.json can be found, then we assume that the vendor folder is that of phpDocumentor itself,
     * which is `../../vendor` starting from this folder.
     *
     * If neither locations exist, then this method returns null because no vendor path could be found.
     *
     * @param string $baseDir parameter for test purposes only.
     * @return string
     */
    public static function findVendorPath($baseDir = __DIR__): string
    {
        // default installation
        $vendorDir = $baseDir . '/../../vendor';
        // Composerised installation, vendor/phpdocumentor/phpdocumentor/src/phpDocumentor is __DIR__
        $rootFolderWhenInstalledWithComposer = $baseDir . '/../../../../../';
        $composerConfigurationPath           = $rootFolderWhenInstalledWithComposer .'composer.json';
        if (file_exists($composerConfigurationPath)) {
            $vendorDir = $rootFolderWhenInstalledWithComposer
                . self::getCustomVendorPathFromComposer($composerConfigurationPath);
        }
        return $vendorDir;
    }
    /**
     * Retrieves the custom vendor-dir from the given composer.json or returns 'vendor'.
     *
     * @param string $composerConfigurationPath the path pointing to the composer.json
     *
     * @return string
     */
    private static function getCustomVendorPathFromComposer($composerConfigurationPath): string
    {
        $composerFile = file_get_contents($composerConfigurationPath);
        $composerJson = json_decode($composerFile, true);
        return $composerJson['config']['vendor-dir'] ?? 'vendor';
    }
}
