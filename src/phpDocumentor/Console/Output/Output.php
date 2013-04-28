<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Console\Output;

use Monolog\Logger;
use Symfony\Component\Console\Output\ConsoleOutput;

class Output extends ConsoleOutput
{
    /** @var Logger */
    protected $logger;

    /**
     * @param \Monolog\Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function writeTimedLog($message, $operation, array $arguments = array())
    {
        $this->write(sprintf('%-68.68s .. ', $message));
        $timerStart = microtime(true);

        call_user_func_array($operation, $arguments);

        $this->writeln(sprintf('%8.3fs', microtime(true) - $timerStart));
    }

    public function write($message, $newline = false, $type = 0)
    {
        if ($this->getLogger()) {
            $this->getLogger()->info($this->getFormatter()->format(strip_tags($message)));
        }

        parent::write($message, $newline, $type);
    }
}
