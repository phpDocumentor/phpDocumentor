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
     * The logger used to capture all messages send by the log method.
     *
     * @see phpDocumentor_Core_Abstract::log()
     *
     * @var phpDocumentor_Core_Log
     */
    static protected $logger = null;

    /**
     * The logger used to capture all messages send by the log method and
     * send them to stdout.
     *
     * @see phpDocumentor_Core_Abstract::log()
     *
     * @var phpDocumentor_Core_Log
     */
    static protected $stdout_logger = null;

    /**
     * The logger used to capture debug messages send by the log method and
     * send them to stdout.
     *
     * @var phpDocumentor_Core_Log
     */
    static protected $debug_logger = null;

    /**
     * The config containing overrides for the defaults.
     *
     * @see phpDocumentor_Core_Abstract::getConfig()
     *
     * @var phpDocumentor_Core_Config
     */
    static protected $config = null;

    /**
     * The current level of logging,
     *
     * This variable is used by i.e. the verbosity flag to enable more or
     * less logging.
     *
     * @var string
     */
    static protected $log_level = null;

    /**
     * Initializes the Debug logger.
     */
    public function __construct()
    {
        if (self::$debug_logger === null) {
            self::$debug_logger = new phpDocumentor_Core_Log(
                $this->getConfig()->logging->paths->errors
            );
        }
    }

    /**
     * Returns the level of messages to log.
     *
     * If no level is set it tries to get the level from the config file.
     *
     * @see Zend_Log
     *
     * @return int
     */
    public function getLogLevel()
    {
        if (self::$log_level === null) {
            $this->setLogLevel($this->getConfig()->logging->level);
        }

        return self::$log_level;
    }

    /**
     * Sets a new level to log messages of.
     *
     * @param string $level Must be one of the Zend_Log LOG_* constants.
     *
     * @see Zend_Log
     *
     * @return void
     */
    public function setLogLevel($level)
    {
        if (!is_numeric($level)) {
            if (!defined('phpDocumentor_Core_Log::' . strtoupper($level))) {
                throw new InvalidArgumentException(
                    'Expected one of the constants of the phpDocumentor_Core_Log class, "'
                    . $level . '" received'
                );
            }

            $constant = 'phpDocumentor_Core_Log::' . strtoupper($level);
            $level = constant($constant);
        }

        if (self::$logger) {
            self::$logger->setThreshold($level);
        }
        if (self::$stdout_logger) {
            self::$stdout_logger->setThreshold($level);
        }

        self::$log_level = $level;
    }

    /**
     * Logs the message to the log with the given priority.
     *
     * This method only works if the Log Level is higher than the given priority.
     * If there is no logger object than this method will instantiate it.
     * In contrary to the debug statement this only logs strings.
     *
     * @param string $message  Element to log.
     * @param int    $priority Priority of the given log.
     *
     * @see DocBlock_Abstract::setLogLevel()
     * @see Zend_Log
     *
     * @return void
     */
    public function log($message, $priority = phpDocumentor_Core_Log::INFO)
    {
        if ($priority == phpDocumentor_Core_Log::DEBUG) {
            self::$debug_logger->log($message, phpDocumentor_Core_Log::DEBUG);
            return;
        }

        if (!self::$logger || !self::$stdout_logger) {
            $config = $this->getConfig();

            // log to file
            self::$logger = new phpDocumentor_Core_Log(
                $config->logging->paths->default
            );
            self::$logger->setThreshold($this->getLogLevel());

            // log to stdout
            self::$stdout_logger = new phpDocumentor_Core_Log(
                phpDocumentor_Core_Log::FILE_STDOUT
            );
            self::$stdout_logger->setThreshold($this->getLogLevel());
        }

        self::$logger->log($message, $priority);
        self::$stdout_logger->log($message, $priority);
    }

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
