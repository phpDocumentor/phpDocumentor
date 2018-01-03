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

namespace phpDocumentor\Command\Helper;

use Monolog\Logger;
use phpDocumentor\Command\Command;
use phpDocumentor\Configuration;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Translator\Translator;
use Pimple\Container;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerHelper extends Helper
{
    /**
     * @var Container
     */
    private $container;

    /**
     * LoggerHelper constructor.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Initializes the given command to accept logging options.
     *
     * This method is intended to be executed once in the Constructor of the given Command as it
     * adds a new option `log`.
     *
     * @param Command $command
     * @return void
     */
    public function addOptions($command)
    {
        $command->addOption('log', null, InputOption::VALUE_OPTIONAL, 'Log file to write to');
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'phpdocumentor_logger';
    }

    /**
     * Connect the logging events to the output object of Symfony Console.
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    public function connectOutputToLogging(OutputInterface $output)
    {
        static $alreadyConnected = false;
        $helper = $this;

        // ignore any second or later invocations of this method
        if ($alreadyConnected) {
            return;
        }

        /** @var Dispatcher $eventDispatcher  */
        $eventDispatcher = $this->container['event_dispatcher'];

        $eventDispatcher->addListener(
            'parser.file.pre',
            function (PreFileEvent $event) use ($output) {
                $output->writeln('Parsing <info>'.$event->getFile().'</info>');
            }
        );

        $eventDispatcher->addListener(
            'system.log',
            function (LogEvent $event) use ($helper, $output) {
                $helper->logEvent($output, $event);
            }
        );

        $alreadyConnected = true;
    }

    /**
     * Logs an event with the output.
     *
     * This method will also colorize the message based on priority and withhold
     * certain logging in case of verbosity or not.
     *
     * @param OutputInterface $output
     * @param LogEvent        $event
     *
     * @return void
     */
    public function logEvent(OutputInterface $output, LogEvent $event)
    {
        $numericErrors = array(
            LogLevel::DEBUG     => 0,
            LogLevel::NOTICE    => 1,
            LogLevel::INFO      => 2,
            LogLevel::WARNING   => 3,
            LogLevel::ERROR     => 4,
            LogLevel::ALERT     => 5,
            LogLevel::CRITICAL  => 6,
            LogLevel::EMERGENCY => 7,
        );

        $threshold = LogLevel::ERROR;
        if ($output->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            $threshold = LogLevel::DEBUG;
        }

        if ($numericErrors[$event->getPriority()] >= $numericErrors[$threshold]) {
            /** @var Translator $translator  */
            $translator = $this->container['translator'];
            $message    = vsprintf($translator->translate($event->getMessage()), $event->getContext());

            switch ($event->getPriority()) {
                case LogLevel::WARNING:
                    $message = '<comment>' . $message . '</comment>';
                    break;
                case LogLevel::EMERGENCY:
                case LogLevel::ALERT:
                case LogLevel::CRITICAL:
                case LogLevel::ERROR:
                    $message = '<error>' . $message . '</error>';
                    break;
            }
            $output->writeln('  ' . $message);
        }
    }
}
