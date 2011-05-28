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
 * Debugger base class.
 *
 * @category DocBlox
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Core_Debug
{
    /**
     * Associative array containing all timers by name.
     *
     * @var float[]
     */
    protected $timer = array();

    /**
     * @var DocBlox_Core_Log
     */
    protected $logger = null;

    /**
     * Initializes this object.
     *
     * @param DocBlox_Core_Log $logger A logger used to output debug messages.
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->resetTimer(); // pre-initialize the default timer
    }

    /**
     * Resets a timer (with the given name) to the current time.
     *
     * @param string $name Name of the used timer.
     *
     * @return void
     */
    public function resetTimer($name = 'default')
    {
        $this->timer[$name] = microtime(true);
    }

    /**
     * Returns the time that has elapsed since the last reset of the timer.
     *
     * @param string $name Name of the used timer.
     *
     * @return float
     */
    public function getElapsedTime($name = 'default')
    {
        return microtime(true) - $this->timer[$name];
    }

    /**
     * Logs a debug message post-fixed with timer information.
     *
     * The string which is sent to the debug logger looks like:
     *
     *     $message in {time} seconds.
     *
     * Thus for readability is advised to write messages as the following
     * examples:
     *
     * * 'Processed parameters' (in 4 seconds)
     * * 'Written to log' (in 4 seconds)
     *
     * @param string $message Text to prefix the default message with.
     * @param string $name    Name of the timer to use.
     *
     * @return void
     */
    public function logWithTimeElapsed($message, $name = 'default')
    {
        $this->log(
            $message . ' in ' . number_format($this->getElapsedTime($name), 4)
            . ' seconds'
        );
        $this->resetTimer($name);
    }

    /**
     * Logs the given to a debug log.
     *
     * If anything other than a string is passed than the item is var_dumped
     * and then stored.
     *
     * @param string|array|object $message Item to log.
     *
     * @return void
     */
    public function log($message)
    {
        $this->logger->log($message, DocBlox_Core_Log::DEBUG);
    }

}