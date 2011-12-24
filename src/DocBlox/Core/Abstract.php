<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Base class used for all classes which need to support logging and core
 * functionality.
 *
 * This class also contains the (leading) current version number.
 *
 * @category DocBlox
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
abstract class DocBlox_Core_Abstract
{
    /** @var string The actual version number of DocBlox. */
    const VERSION = '0.17.2';

    /**
     * The logger used to capture all messages send by the log method.
     *
     * @see DocBlox_Core_Abstract::log()
     *
     * @var DocBlox_Core_Log
     */
    static protected $logger = null;

    /**
     * The debugger used to time events and write to the debugging log.
     *
     * @var DocBlox_Core_Debug
     */
    protected $debugger = null;

    /**
     * The logger used to capture all messages send by the log method and
     * send them to stdout.
     *
     * @see DocBlox_Core_Abstract::log()
     *
     * @var DocBlox_Core_Log
     */
    static protected $stdout_logger = null;

    /**
     * The logger used to capture debug messages send by the log method and
     * send them to stdout.
     *
     * @var DocBlox_Core_Log
     */
    static protected $debug_logger = null;

    /**
     * The config containing overrides for the defaults.
     *
     * @see DocBlox_Core_Abstract::getConfig()
     *
     * @var DocBlox_Core_Config
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
     * Initializes the Debugger.
     *
     * @todo move the loggers to a DIC instead of statics!!!
     */
    public function __construct()
    {
        if (self::$debug_logger === null) {
            self::$debug_logger = new DocBlox_Core_Log(
                $this->getConfig()->logging->paths->errors
            );
        }

        $this->setDebugger(
            new DocBlox_Core_Debug(self::$debug_logger)
        );
    }

    /**
     * Sets the debugger for this session; automatically enables debugging mode.
     *
     * @param DocBlox_Core_Debug $debugger Debugger to use for this session.
     *
     * @return void
     */
    public function setDebugger($debugger)
    {
        $this->debugger = $debugger;
    }

    /**
     * Resets a timer (with the given name) to the current time.
     *
     * @param string $name Name of the used timer.
     *
     * @todo change implementations of this method to use the debugger directly.
     *
     * @return void
     */
    protected function resetTimer($name = 'default')
    {
        if ($this->debugger) {
            $this->debugger->resetTimer($name);
        }
    }

    /**
     * Returns the time that has elapsed since the last reset of the timer.
     *
     * If debugging is not enabled this method will return -1.
     *
     * @param string $name Name of the timer.
     *
     * @return float
     */
    protected function getElapsedTime($name = 'default')
    {
        return ($this->debugger)
                ? $this->debugger->getElapsedTime($name)
                : -1;
    }

    /**
     * Returns the level of messages to log.
     *
     * If no level is set it tries to get the level from the config file.
     *
     * @see Zend_Log
     *
     * @return void
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
            if (!defined('DocBlox_Core_Log::' . strtoupper($level))) {
                throw new InvalidArgumentException(
                    'Expected one of the constants of the DocBlox_Core_Log class, "'
                    . $level . '" received'
                );
            }

            $constant = 'DocBlox_Core_Log::' . strtoupper($level);
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
     * Logs a debug message post-fixed with timer information.
     *
     * The string which is sent to the debug logger looks like:
     *
     *     $message in {time} seconds.
     *
     * Thus for readability is advised to write messages as the following examples:
     *
     * * 'Processed parameters' (in 4 seconds)
     * * 'Written to log' (in 4 seconds)
     *
     * @param string $message Text to prepend the pre-fab text with.
     * @param string $name    Name of the timer.
     *
     * @return void
     */
    protected function debugTimer($message, $name = 'default')
    {
        if ($this->debugger) {
            $this->debugger->logWithTimeElapsed($message, $name);
        }
    }

    /**
     * Logs the given to a debug log.
     *
     * This method only works if the Log Level is higher than DEBUG.
     * If anything other than a string is passed than the item is var_dumped and
     * then stored.
     * If there is no debug logger object than this method will instantiate it.
     *
     * @param string|array|object $message Element to log.
     *
     * @see DocBlock_Abstract::setLogLevel()
     * @see Zend_Log
     *
     * @return void
     */
    protected function debug($message)
    {
        if ($this->debugger) {
            $this->debugger->log($message);
        }
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
    public function log($message, $priority = DocBlox_Core_Log::INFO)
    {
        if ($priority == DocBlox_Core_Log::DEBUG) {
            $this->debug($message);
            return;
        }

        if (!self::$logger || !self::$stdout_logger) {
            $config = $this->getConfig();

            // log to file
            self::$logger = new DocBlox_Core_Log($config->logging->paths->default);
            self::$logger->setThreshold($this->getLogLevel());

            // log to stdout
            self::$stdout_logger = new DocBlox_Core_Log(
                DocBlox_Core_Log::FILE_STDOUT
            );
            self::$stdout_logger->setThreshold($this->getLogLevel());
        }

        self::$logger->log($message, $priority);
        self::$stdout_logger->log($message, $priority);
    }

    /**
     * Returns the configuration for DocBlox.
     *
     * @return DocBlox_Core_Config
     */
    public function getConfig()
    {
        return self::config();
    }


    /**
     * Set a custom DocBlox configuration
     *
     * @param DocBlox_Core_Config $config Configuration file to use in the project.
     *
     * @return void
     */
    public static function setConfig(DocBlox_Core_Config $config)
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
     * Returns the configuration for DocBlox.
     *
     * @return DocBlox_Core_Config
     */
    public static function config()
    {
        if (self::$config === null) {
            self::$config = new DocBlox_Core_Config(
                dirname(__FILE__) . '/../../../data/docblox.tpl.xml'
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
        echo 'DocBlox version ' . DocBlox_Core_Abstract::VERSION
             . PHP_EOL
             . PHP_EOL;
    }
}
