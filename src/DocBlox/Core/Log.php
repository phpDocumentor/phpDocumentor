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
 * Logger used to capture any messages via any stream.
 *
 * @category DocBlox
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Core_Log
{
    /** @var string Emergency: system is unstable */
    const EMERG = Zend_Log::EMERG;

    /** @var string Alert: action must be taken immediately */
    const ALERT = Zend_Log::ALERT;

    /** @var string Critical: critical conditions */
    const CRIT = Zend_Log::CRIT;

    /** @var string Error: error conditions */
    const ERR = Zend_Log::ERR;

    /** @var string Warning: warning conditions */
    const WARN = Zend_Log::WARN;

    /** @var string Notice: normal but significant condition */
    const NOTICE = Zend_Log::NOTICE;

    /** @var string Informational: informational messages */
    const INFO = Zend_Log::INFO;

    /** @var string Debug: debug messages */
    const DEBUG = Zend_Log::DEBUG;

    /** @var string Quiet: disables logging */
    const QUIET = -1;

    /** @var string Output will only be sent to stdout */
    const FILE_STDOUT = 'php://stdout';

    /** @var int Only log messages that equal or exceed this. */
    protected $threshold = self::DEBUG;

    /** @var string The name of the file/stream where the logs are written to. */
    protected $filename = '';

    /** @var Zend_Log The logger to use for storing information. */
    protected $logger = null;

    /**
     * Initialize the logger.
     *
     * @param string $file May also be the FILE_STDOUT constant to output to STDOUT.
     */
    public function __construct($file)
    {
        // only do the file checks if it is an actual file.
        if ($file !== self::FILE_STDOUT) {
            // replace APP_ROOT and DATE variables
            $file = str_replace(
                array(
                     '{APP_ROOT}',
                     '{DATE}'
                ),
                array(
                     DocBlox_Core_Abstract::config()->paths->application,
                     date('YmdHis')
                ),
                $file
            );

            // check if the given file location is writable; if not: output an error
            if (!is_writeable(dirname($file))) {
                $this->logger = new Zend_Log(new Zend_Log_Writer_Null());
                $this->log(
                    'The log directory does not appear to be writable; tried '
                    . 'to log to: ' . $file . ', disabled logging to file',
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
     * Returns the name of the file/stream where the output is written to or
     * null if it is send to the void.
     *
     * @return null|string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets the logging threshold; anything more detailed than the given level
     * will not be logged.
     *
     * @param int $threshold The min level that will be logged.
     *
     * @return void
     */
    public function setThreshold($threshold)
    {
        if (is_object($threshold) && (get_class($threshold) === 'sfEvent')) {
            $threshold = $threshold[0];
        }

        if (!is_numeric($threshold)) {
            if (!defined('DocBlox_Core_Log::' . strtoupper($threshold))) {
                throw new InvalidArgumentException(
                    'Expected one of the constants of the DocBlox_Core_Log class, '
                    . '"' . $threshold . '" received'
                );
            }
            $constant = 'DocBlox_Core_Log::' . strtoupper($threshold);
            $threshold = constant($constant);
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
     * Log the given data; if it is something else than a string it will be
     * var_dumped and then logged.
     *
     * @param mixed      $data  The data to log.
     * @param int|string $level The level of the message to log.
     *
     * @return void
     */
    public function log($data, $level = self::INFO)
    {
        // we explicitly use the get_class method to prevent a hard dependency
        // to the sfEvent class; this way the connection is implicit and doesn't
        // it matter to DocBlox_Core_Log whether it is loaded or not.
        if (is_object($data) && (get_class($data) === 'sfEvent')) {
            // if this is an event; replace our data to cope with that
            $level = $data['priority'];
            $data  = $data['message'];
        }

        // is the log level is below the priority; just skip this
        if ($this->getThreshold() < $level) {
            return;
        }

        // if the given is not a string then we var dump the object|array to
        // inspect it
        if (!is_string($data)) {
            ob_start();
            var_dump($data);
            $data = ob_get_clean();
        }

        $memory = number_format(round(memory_get_usage() / 1024 / 1024, 2), 2);
        $data = (($this->getThreshold() == Zend_Log::DEBUG)
            ? '[' . $memory . 'mb]: '
            : '')
            . $data;

        $this->logger->log($data, $level);
    }

}