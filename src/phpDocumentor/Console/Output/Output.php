<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Console\Output;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * A custom output class for our Console object that supports writing to the log.
 */
class Output extends ConsoleOutput
{
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
}
