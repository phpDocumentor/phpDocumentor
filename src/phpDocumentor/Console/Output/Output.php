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

namespace phpDocumentor\Console\Output;

use Monolog\Logger;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * A custom output class for our Console object that supports writing to the log.
 */
class Output extends ConsoleOutput
{
    /** @var Logger Object used to log system and debug messages to. */
    protected $logger;

    /**
     * Sets a logger object to write information to.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns the object where is being logged to.
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Executes a callable piece of code and writes an entry to the log detailing how long it took.
     *
     * @param string   $message
     * @param callable $operation
     * @param array    $arguments
     *
     * @return void
     */
    public function writeTimedLog($message, $operation, array $arguments = array())
    {
        $this->write(sprintf('%-66.66s .. ', $message));
        $timerStart = microtime(true);

        call_user_func_array($operation, $arguments);

        $this->writeln(sprintf('%8.3fs', microtime(true) - $timerStart));
    }

    /**
     * Write an entry to the console and to the provided logger.
     *
     * @param array|string $message
     * @param bool         $newline
     * @param int          $type
     *
     * @return void
     */
    public function write($message, $newline = false, $type = 0)
    {
        $messages = (array) $message;

        if ($this->getLogger()) {
            foreach ($messages as $message) {
                $this->getLogger()->info($this->getFormatter()->format(strip_tags($message)));
            }
        }

        parent::write($messages, $newline, $type);
    }
}
