<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Base class used for all classes which need to support logging and core
 * functionality.
 *
 * This class also contains the (leading) current version number.
 *
 * @category phpDocumentor
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
abstract class phpDocumentor_Core_Abstract
{
    /** @var string The actual version number of phpDocumentor. */
    const VERSION = '2.0.0a2';

    /**
     * The config containing overrides for the defaults.
     *
     * @see phpDocumentor_Core_Abstract::getConfig()
     *
     * @var phpDocumentor_Core_Config
     */
    static protected $config = null;

    /**
     * Returns the configuration for phpDocumentor.
     *
     * @return phpDocumentor_Core_Config
     */
    public function getConfig()
    {
        return self::config();
    }

    /**
     * Set a custom phpDocumentor configuration
     *
     * @param phpDocumentor_Core_Config $config Configuration file to use in the project.
     *
     * @return void
     */
    public static function setConfig(phpDocumentor_Core_Config $config)
    {
        self::$config = $config;
    }

    /**
     * Reset the configuration.
     *
     * @return void
     */
    public static function resetConfig()
    {
        self::$config = null;
    }

    /**
     * Returns the configuration for phpDocumentor.
     *
     * @return phpDocumentor_Core_Config
     */
    public static function config()
    {
        if (self::$config === null) {
            self::$config = new phpDocumentor_Core_Config(
                dirname(__FILE__) . '/../../../data/phpdoc.tpl.xml'
            );
        }

        return self::$config;
    }

    /**
     * Returns the version header.
     *
     * @return string
     */
    public static function renderVersion()
    {
        echo 'phpDocumentor version ' . phpDocumentor_Core_Abstract::VERSION
             . PHP_EOL
             . PHP_EOL;
    }
}
