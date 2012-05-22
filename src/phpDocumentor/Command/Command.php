<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

class Command extends \Cilex\Command\Command
{
    protected function getProgressBar(InputInterface $input)
    {
        if (!$input->getOption('progressbar')) {
            return null;
        }

        return $this->getHelperSet()->get('progress');
    }

    /**
     * Connect the logging events to the output object of Symfony Console.
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function connectOutputToLogging(OutputInterface $output)
    {
        /** @var \sfEventDispatcher $event_dispatcher  */
        $event_dispatcher = $this->getService('event_dispatcher');
        $command = $this;

        $event_dispatcher->connect(
            'system.log',
            function(\sfEvent $event) use ($command, $output) {
                $command->logEvent($output, $event);
            }
        );

        $event_dispatcher->connect(
            'system.debug',
            function(\sfEvent $event) use ($command, $output) {
                $command->logEvent($output, $event);
            }
        );
    }

    /**
     * Logs an event with the output.
     *
     * This method will also colorize the message based on priority and withhold
     * certain logging in case of verbosity or not.
     *
     * @param OutputInterface $output
     * @param \sfEvent $event
     *
     * @return void.
     */
    public function logEvent(OutputInterface $output, \sfEvent $event)
    {
        if (!isset($event['priority'])) {
            $event['priority'] = \phpDocumentor\Plugin\Core\Log::INFO;
        }

        $threshold = \phpDocumentor\Plugin\Core\Log::ERR;
        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $threshold = \phpDocumentor\Plugin\Core\Log::DEBUG;
        }

        if ($event['priority'] <= $threshold) {
            $message = $event['message'];
            switch ($event['priority'])
            {
            case \phpDocumentor\Plugin\Core\Log::WARN:
                $message = '<comment>' . $message . '</comment>';
                break;
            case \phpDocumentor\Plugin\Core\Log::EMERG:
            case \phpDocumentor\Plugin\Core\Log::ALERT:
            case \phpDocumentor\Plugin\Core\Log::CRIT:
            case \phpDocumentor\Plugin\Core\Log::ERR:
                $message = '<error>' . $message . '</error>';
                break;
            }
            $output->writeln('  ' . $message);
        }
    }

}
