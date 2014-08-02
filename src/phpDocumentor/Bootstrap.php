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
	    $vendorDir = $this->determineVendorDir();

        $autoloader = $this->createAutoloader($vendorDir);
		$this->addDompdfConfig($vendorDir);

        return new Application($autoloader);
    }

    /**
     * Sets up XHProf so that we can profile phpDocumentor using XHGUI.
     *
     * @return self
     */
    public function registerProfiler()
    {
        // check whether xhprof is loaded
        $profile   = (bool)(getenv('PHPDOC_PROFILE') === 'on');
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
     * @throws \RuntimeException if no autoloader could be found.
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function createAutoloader($vendorDir = 'vendor')
    {
        $autoloader_base_path = '/../../' . $vendorDir . '/autoload.php';

        // if the file does not exist from a base path it is included as vendor
        $autoloader_location = file_exists(__DIR__ . $autoloader_base_path)
            ? __DIR__ . $autoloader_base_path
            : __DIR__ . '/../../../..' . $autoloader_base_path;

        if (! file_exists($autoloader_location) || ! is_readable($autoloader_location)) {
            throw new \RuntimeException('Unable to find autoloader at ' . $autoloader_location);
        }

        return require $autoloader_location;
    }
	
	protected function determineVendorDir()
	{
	    $vendorDir = 'vendor';

	    if (strpos('@php_dir@', '@php_dir') === 0) {
		    $vendorDir = $this->getVendorPath('@php_dir@/phpDocumentor/composer.json');
		} elseif (file_exists(__DIR__ . '/../../../../../../composer.json')) {
		    $vendorDir = $this->getVendorPath(__DIR__ . '/../../../../../../composer.json');
		}

		return $vendorDir;
	}
	
	protected function getVendorPath($path)
	{
	    $composerFile = file_get_contents($path);
		$composerJson = json_decode($composerFile, true);

		return isset($composerJson['config']['vendor-dir']) ? $composerJson['config']['vendor-dir'] : 'vendor';
	}
	
	protected function addDompdfConfig($vendorDir)
	{	    
		if (!\Phar::running()) {
			define('DOMPDF_ENABLE_AUTOLOAD', false);
			if (file_exists(__DIR__ . '/../../' . $vendorDir . '/dompdf/dompdf/dompdf_config.inc.php')) {
				// when normally installed, get it from the vendor folder
				require_once(__DIR__ . '/../../' . $vendorDir . '/dompdf/dompdf/dompdf_config.inc.php');
			} else {
				// when installed using composer, include it from that location
				require_once(__DIR__ . '/../../../../dompdf/dompdf/dompdf_config.inc.php');
			}
		}
	}
}
