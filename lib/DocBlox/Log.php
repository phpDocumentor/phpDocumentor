<?php
/**
 * This class handles the logging for DocBlox.
 *
 * @package    DocBlox
 * @subpackage Logging
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Log
{
  /*
   * Constants are reproduced here to make sure that the logger is independant of Zend_Log; should we ever want
   * to switch.
   */
  const EMERG   = Zend_Log::EMERG; // Emergency: system is unusable
  const ALERT   = Zend_Log::ALERT; // Alert: action must be taken immediately
  const CRIT    = Zend_Log::CRIT; // Critical: critical conditions
  const ERR     = Zend_Log::ERR; // Error: error conditions
  const WARN    = Zend_Log::WARN; // Warning: warning conditions
  const NOTICE  = Zend_Log::NOTICE; // Notice: normal but significant condition
  const INFO    = Zend_Log::INFO; // Informational: informational messages
  const DEBUG   = Zend_Log::DEBUG; // Debug: debug messages

  const FILE_STDOUT = 'php://stdout';

  /**
   * Only log messages that equal or exceed this.
   *
   * @var int
   */
  protected $threshold = self::DEBUG;

  /**
   * The name of the file/stream where the logs are written to.
   *
   * @var string
   */
  protected $filename = '';

  /**
   * The logger to use for storing information.
   *
   * @var Zend_Log
   */
  protected $logger = null;

  /**
   * Initialize the logger.
   *
   * @param string $file May also be the FILE_STDOUT constant to output to STDOUT.
   */
  public function __construct($file)
  {
    // only do the file checks if it is an actual file.
    if ($file !== self::FILE_STDOUT)
    {
      // replace APP_ROOT and DATE variables
      $file = str_replace(
        array(
          '{APP_ROOT}',
          '{DATE}'
        ),
        array(
          DocBlox_Abstract::config()->paths->application,
          date('YmdHis')
        ),
        $file
      );

      // check if the given file location is writable; if not: output an error
      if (!is_writeable(dirname($file)))
      {
        $this->logger = new Zend_Log(new Zend_Log_Writer_Null());
        $this->log(
          'The log directory does not appear to be writable; tried to log to: ' . $file . ', disabled logging to file',
          self::ERR
        );

        $this->filename = null;
        return;
      }
    }

    $this->filename = $file;
    $this->logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen($file, 'w')));
  }

  /**
   * Returns the name of the file/stream where the output is written to or null if it is send to the void.
   *
   * @return null|string
   */
  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * Sets the logging threshold; anything more detailed than the given level will not be logged.
   *
   * @param int $threshold
   *
   * @return void
   */
  public function setThreshold($threshold)
  {
    if (!is_numeric($threshold))
    {
      if (!defined('DocBlox_Log::' . strtoupper($threshold)))
      {
        throw new InvalidArgumentException(
          'Expected one of the constants of the DocBlox_Log class, "' . $threshold . '" received'
        );
      }
      $threshold = constant('DocBlox_Log::' . strtoupper($threshold));
    }

    $this->threshold = $threshold;
  }

  /**
   * Returns the threshold for this logger.
   *
   * @return int
   */
  public function getThreshold()
  {
    return $this->threshold;
  }

  /**
   * Log the given data; if it is something else than a string it will be var_dumped and then logged.
   *
   * @param mixed $data
   * @param int   $level
   *
   * @return void
   */
  public function log($data, $level = self::INFO)
  {
    // is the log level is below the priority; just skip this
    if ($this->getThreshold() < $level)
    {
      return;
    }

    // if the given is not a string then we var dump the object|array to inspect it
    if (!is_string($data))
    {
      ob_start();
      var_dump($data);
      $data = ob_get_clean();
    }

    $data = (($this->getThreshold() == Zend_Log::DEBUG)
      ? '[' . number_format(round(memory_get_usage() / 1024 / 1024, 2), 2) . 'mb]: '
      : '')
      . $data;

    $this->logger->log($data, $level);
  }

}