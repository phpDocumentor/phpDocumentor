<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Base
 * @copyright  Copyright (c) 2010-2010 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Base class used for all classes which need to support logging and core functionality.
 *
 * This class also contains the (leading) current version number.
 *
 * @category   DocBlox
 * @package    Base
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
abstract class DocBlox_Abstract
{
  /**
   * The actual version number of DocBlox.
   *
   * @var int
   */
  const VERSION = '0.8.0-DEV';

  /**
   * The logger used to capture all messages send by the log method.
   *
   * @see DocBlox_Abstract::log()
   * @var Zend_Log
   */
  static protected $logger       = null;

  /**
   * The config containing overrides for the defaults.
   *
   * @see DocBlox_Abstract::getConfig()
   * @var Zend_Config
   */
  static protected $config       = null;

  /**
   * The logger used to capture the debug messages send by the debug method.
   *
   * @see DocBlox_Abstract::debug()
   * @var Zend_Log
   */
  static protected $debug_logger = null;

  /**
   * The current level of logging,
   *
   * This variable is used by i.e. the verbosity flag to enable more or less logging.
   *
   * @var string
   */
  static protected $log_level    = null;

  /**
   * Associative array containing all timers by name.
   *
   * @var sfTimer[]
   */
  protected $timer = array();

  /**
   * Initializes the default timer.
   *
   * @return void
   */
  public function __construct()
  {
    $this->resetTimer();
  }

  /**
   * Resets a timer (with the given name) to the current time.
   *
   * @param  string $name
   * @return void
   */
  protected function resetTimer($name = 'default')
  {
    $this->timer[$name] = microtime(true);
  }

  /**
   * Returns the time that has elapsed since the last reset of the timer.
   *
   * @param  string $name
   * @return mixed
   */
  protected function getElapsedTime($name = 'default')
  {
    return microtime(true) - $this->timer[$name];
  }

  /**
   * Returns the level of messages to log.
   *
   * If no level is set it tries to get the level from the config file.
   * @see    Zend_Log
   * @return void
   */
  public function getLogLevel()
  {
    if (self::$log_level === null)
    {
      $this->setLogLevel($this->getConfig()->logging->level);
    }

    return self::$log_level;
  }

  /**
   * Sets a new level to log messages of.
   *
   * @param  string $level Must be one of the Zend_Log LOG_* constants
   * @see    Zend_Log
   * @return void
   */
  public function setLogLevel($level)
  {
    if (!is_numeric($level))
    {
      $level = constant('Zend_Log::'.strtoupper($level));
    }

    self::$log_level = $level;
  }

  /**
   * Logs a debug message post-fixed with timer information.
   *
   * The string which is sent to the debug logger looks like:
   * $message in {time} seconds.
   * Thus for readability is advised to write messages as the following examples:
   * - 'Processed parameters' (in 4 seconds)
   * - 'Written to log' (in 4 seconds)
   *
   * @param  string $message
   * @param  string $name
   * @return void
   */
  protected function debugTimer($message, $name = 'default')
  {
    $this->debug($message.' in '.number_format($this->getElapsedTime($name), 4).' seconds');
    $this->resetTimer($name);
  }

  /**
   * Logs the given to a debug log.
   *
   * This method only works if the Log Level is higher than DEBUG.
   * If anything other than a string is passed than the item is var_dumped and then stored.
   * If there is no debug logger object than this method will instantiate it.
   *
   * @see    DocBlock_Abstract::setLogLevel()
   * @see    Zend_Log
   * @param  string|array|object $message
   * @return void
   */
  protected function debug($message)
  {
    // is the log level is below debugging; just skip this
    if ($this->getLogLevel() < Zend_Log::DEBUG)
    {
      return;
    }

    if (!self::$debug_logger)
    {
      $file = str_replace(
        array('{APP_ROOT}', '{DATE}'),
        array(realpath(dirname(__FILE__) . '/..'), date('YmdHis')),
        $this->getConfig()->logging->paths->errors
      );

      self::$debug_logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen($file, 'w')));
    }

    // if the given is not a string then we var dump the object|array to inspect it
    $dump = $message;
    if (!is_string($dump))
    {
      ob_start();
      var_dump($message);
      $dump = ob_get_clean();
    }

    self::$debug_logger->log($dump, Zend_Log::DEBUG);
  }

  /**
   * Logs the message to the log with the given priority.
   *
   * This method only works if the Log Level is higher than the given priority.
   * If there is no logger object than this method will instantiate it.
   * In contrary to the debug statement this only logs strings.
   *
   * @see    DocBlock_Abstract::setLogLevel()
   * @see    Zend_Log
   * @param  string $message
   * @return void
   */
  public function log($message, $priority = Zend_Log::INFO)
  {
    // is the log level is below the priority; just skip this
    if ($this->getLogLevel() < $priority)
    {
      return;
    }

    if ($priority == Zend_Log::DEBUG)
    {
      $this->debug($message);
      return;
    }

    if (!self::$logger)
    {
      $file = str_replace(
        array('{APP_ROOT}', '{DATE}'),
        array(realpath(dirname(__FILE__).'/..'), date('YmdHis')),
        $this->getConfig()->logging->paths->default
      );

      self::$logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen($file, 'w')));
    }

    static $priority_names = null;
    if ($priority_names === null)
    {
      $r = new ReflectionClass('Zend_Log');
      $priority_names = array_flip($r->getConstants());
    }

    $debug_info = ($this->getLogLevel() == Zend_Log::DEBUG) ? ', '.round(memory_get_usage() / 1024 / 1024, 2).'mb' : '';
    echo '['.$priority_names[$priority].': '.date('H:i').$debug_info.']: '.$message.PHP_EOL;
    self::$logger->log($message, $priority);
  }

  /**
   * Returns the configuration for DocBlox.
   *
   * @return Zend_Config
   */
  public function getConfig()
  {
    return self::config();
  }

  /**
   * Loads the configuration for DocBlox.
   *
   * @param string $filename
   */
  public static function loadConfig($filename)
  {
    if (!is_readable($filename))
    {
      throw new Exception('Config file "'.$filename.'" is not readable');
    }

    self::$config = new Zend_Config_Xml(file_get_contents($filename), null, true);
  }

  /**
   * Returns the configuration for DocBlox.
   *
   * @return Zend_Config
   */
  public static function config()
  {
    if (self::$config === null)
    {
      self::loadConfig(dirname(__FILE__).'/../config.xml');
    }

    return self::$config;
  }
}