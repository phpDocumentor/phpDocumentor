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
    public static function findVendorPath(string $baseDir = __DIR__)
    {
        // Composerised installation, vendor/phpdocumentor/phpdocumentor/src/phpDocumentor is __DIR__
        $vendorFolderWhenInstalledWithComposer = $baseDir . '/../../../../';
        if (file_exists($vendorFolderWhenInstalledWithComposer . '/autoload.php')) {
            $vendorDir = $vendorFolderWhenInstalledWithComposer;
        } else {
            // Repository cloned via git
            $vendorDir = $baseDir . '/../../' . static::getCustomVendorPathFromComposer(
                $baseDir . '/../../' . static::findComposerConfigurationPath()
            );
        }

        // Do not use realpath() here to don't break installation from phar
        if (!file_exists($vendorDir)) {
            throw new RuntimeException('Unable to find vendor directory for ' . $baseDir);
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
    protected function getCustomVendorPathFromComposer($composerConfigurationPath)
    {
        $composerFile = file_get_contents($composerConfigurationPath);
        $composerJson = json_decode($composerFile, true);

        return isset($composerJson['config']['vendor-dir']) ? $composerJson['config']['vendor-dir'] : 'vendor';
    }
}
