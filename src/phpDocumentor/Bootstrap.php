<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Composer\Autoload\ClassLoader;

/**
 * This class provides a bootstrap for all application who wish to interface with phpDocumentor.
 *
 * The Bootstrapper is responsible for setting up the autoloader, profiling options and application, including
 * dependency injection container.
 *
 * The simplest usage would be:
 *
 *     $app = Bootstap::createInstance()->initialize();
 *
 * This will setup the autoloader and application, including Service Container, and return an instance of the
 * application ready to be ran using the `run` command.
 *
 * If you need more control you can do some of the steps manually:
 *
 *     $bootstrap = Bootstap::createInstance();
 *     $autoloader = $bootstrap->createAutoloader();
 *     $app = new Application($autoloader)
 */
class Bootstrap
{
    /**
     * Helper static function to get an instance of this class.
     *
     * Usually used to do a one-line initialization, such as:
     *
     *     \phpDocumentor\Bootstrap::createInstance()->initialize();
     *
     * @return Bootstrap
     */
    public static function createInstance()
    {
        return new self();
    }

    /**
     * Convenience method that does the complete initialization for phpDocumentor.
     *
     * @return Application
     */
    public function initialize()
    {
        $vendorPath = $this->findVendorPath();

        $autoloader = $this->createAutoloader($vendorPath);

        return new Application($autoloader, array('composer.vendor_path' => $vendorPath));
    }

    /**
     * Sets up XHProf so that we can profile phpDocumentor using XHGUI.
     *
     * @return self
     */
    public function registerProfiler()
    {
        // check whether xhprof is loaded
        $profile = (bool) (getenv('PHPDOC_PROFILE') === 'on');
        $xhguiPath = getenv('XHGUI_PATH');
        if ($profile && $xhguiPath && extension_loaded('xhprof')) {
            echo 'PROFILING ENABLED' . PHP_EOL;
            include($xhguiPath . '/external/header.php');
        }

        return $this;
    }

    /**
     * Initializes and returns the autoloader.
     *
     * @param string|null $vendorDir A path (either absolute or relative to the current working directory) leading to
     *     the vendor folder where composer installed the dependencies.
     *
     * @throws \RuntimeException if no autoloader could be found.
     *
     * @return ClassLoader
     */
    public function createAutoloader($vendorDir = null)
    {
        if (! $vendorDir) {
            $vendorDir = __DIR__ . '/../../vendor';
        }

        $autoloader_location = $vendorDir . '/autoload.php';
        if (! file_exists($autoloader_location) || ! is_readable($autoloader_location)) {
            throw new \RuntimeException(
                'phpDocumentor expected to find an autoloader at "' . $autoloader_location . '" but it was not there. '
                . 'Usually this is because the "composer install" command has not been ran yet. If this is not the '
                . 'case, please open an issue at http://github.com/phpDocumentor/phpDocumentor2 detailing what '
                . 'installation method you used, which path is mentioned in this error message and any other relevant '
                . 'information.'
            );
        }

        return require $autoloader_location;
    }

    /**
     * Attempts to find the location of the vendor folder.
     *
     * This method tries to check for a composer.json in a directory 5 levels shallower than the folder of this file.
     * This is the expected location if phpDocumentor is installed using composer because the current directory for
     * this file is expected to be 'vendor/phpdocumentor/phpdocumentor/src/phpDocumentor'.
     *
     * If a composer.json is not found in the aforementioned directory, we search in gradually shallower directories
     * until we find a composer.json or reach the filesystem root.
     *
     * If a composer.json is found we will try to extract the vendor folder name using the 'vendor-dir' configuration
     * option of composer or assume it is vendor if that option is not set.
     *
     * If no custom composer.json can be found, then we assume that the vendor folder is that of phpDocumentor itself.
     *
     * If no vendor directory was found, null is returned.
     *
     * @param $baseDir parameter for test purposes only.
     * @return string|null
     */
    public function findVendorPath($baseDir = __DIR__)
    {
        $standardRootDir = $baseDir . '/../../../../..';
        $phpDocumentorVendorDir = $baseDir . '/../../vendor';

        // Are we composer installed with standard vendor-dir in composer.json?
        $composerJson = $standardRootDir . '/composer.json';
        if (file_exists($composerJson)) {
            // e.g. /home/user/my-project/composer.json

            $relativeVendorDir = $this->getCustomVendorPathFromComposer($composerJson);

            if (is_dir($standardRootDir . '/' . $relativeVendorDir)) {
                // e.g. /home/user/my-project/vendor
                // or, if vendor-dir is configured in composer.json:
                //      /home/user/my-project/custom-vendor-dir
                return $standardRootDir . '/' . $relativeVendorDir;
            } else {
                throw new \RuntimeException(
                    'Found composer.json, but the vendor-dir is missing.'
                    . " (composer.json found at {$standardRootDir}/composer.json)"
                    . " (vendor-dir should be at {$standardRootDir}/{$relativeVendorDir})"
                );
            }
        } elseif (is_dir($phpDocumentorVendorDir)) {
            // e.g. /path/to/clone/of/phpDocumentor2/vendor
            return $phpDocumentorVendorDir;
        } else { // Look for a composer.json in shallower paths
            $rootCandidate = $standardRootDir;

            do {
                $rootCandidate = "{$rootCandidate}/..";

                $composerJson = $rootCandidate . '/composer.json';

                if (file_exists($composerJson)) {
                    $relativeVendorDir = $this->getCustomVendorPathFromComposer($composerJson);

                    return $rootCandidate . '/' . $relativeVendorDir;
                }
            } while (is_dir($rootCandidate));
        }

        return null;
    }

    /**
     * Retrieves the custom vendor-dir from the given composer.json or returns 'vendor'.
     *
     * @param string $composerConfigurationPath the path pointing to the composer.json
     *
     * @return string
     */
    protected function getCustomVendorPathFromComposer($composerConfigurationPath)
    {
        $composerFile = file_get_contents($composerConfigurationPath);
        $composerJson = json_decode($composerFile, true);

        return isset($composerJson['config']['vendor-dir']) ? $composerJson['config']['vendor-dir'] : 'vendor';
    }
}
