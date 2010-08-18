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
  const VERSION = '0.6.0-DEV';

  /**
   * The logger used to capture all messages send by the log method.
   *
   * @see DocBlox_Abstract::log()
   * @var Zend_Log
   */
  static protected $logger       = null;

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
  static protected $log_level    = Zend_Log::WARN;

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
   * Sets a new level to log messages of.
   *
   * @param  string $level Must be one of the Zend_Log LOG_* constants
   * @see    Zend_Log
   * @return void
   */
  public function setLogLevel($level)
  {
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
    if (self::$log_level < Zend_Log::DEBUG)
    {
      return;
    }

    if (!self::$debug_logger)
    {
      // TODO convert to loading from config
      self::$debug_logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen('log/'.date('YmdHis').'.debug.log', 'w')));
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
    if (self::$log_level < $priority)
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
      // TODO convert to loading from config
      self::$logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen('log/'.date('YmdHis').'.errors.log', 'w')));
    }

    static $priority_names = null;
    if ($priority_names === null)
    {
      $r = new ReflectionClass('Zend_Log');
      $priority_names = array_flip($r->getConstants());
    }

    echo '['.$priority_names[$priority].': '.date('H:i').']: '.$message.PHP_EOL;
    self::$logger->log($message, $priority);
  }

}