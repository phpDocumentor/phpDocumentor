<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\I18n\Translator\Translator;
use phpDocumentor\Event\DebugEvent;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Parser\Event\PreFileEvent;

/**
 * Base command for phpDocumentor commands.
 *
 * Includes additional methods to forward the output to the logging events
 * of phpDocumentor.
 */
class Command extends \Cilex\Command\Command
{
    /**
     * Returns boolean based on whether given path is absolute or not.
     *
     * @param string $path Given path
     *
     * @author Michael Wallner <mike@php.net>
     *
     * @link http://pear.php.net/package/File_Util/docs/latest/File/File_Util/
     *     File_Util.html#methodisAbsolute
     *
     * @todo consider moving this method to a more logical place
     *
     * @return boolean True if the path is absolute, false if it is not
     */
    protected function isAbsolute($path)
    {
        if (preg_match('/(?:\/|\\\)\.\.(?=\/|$)/', $path)) {
            return false;
        }

        // windows detection
        if (defined('OS_WINDOWS') ? OS_WINDOWS : !strncasecmp(PHP_OS, 'win', 3)) {
            return (($path[0] == '/') || preg_match('/^[a-zA-Z]:(\\\|\/)/', $path));
        }

        return ($path[0] == '/') || ($path[0] == '~');
    }

    /**
     * Returns the Progress bar helper.
     *
     * With this helper it is possible to display a progress bar and make it
     * fill.
     *
     * @param InputInterface $input
     *
     * @return \phpDocumentor\Console\Helper\ProgressHelper
     */
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
        static $already_connected = false;

        // ignore any second or later invocations of this method
        if ($already_connected) {
            return;
        }

        /** @var \phpDocumentor\Event\Dispatcher $event_dispatcher  */
        $event_dispatcher = $this->getService('event_dispatcher');

        /** @var Command $command  */
        $command = $this;

        $event_dispatcher->addListener(
            'parser.file.pre',
            function (PreFileEvent $event) use ($output) {
                $output->writeln('Parsing <info>'.$event->getFile().'</info>');
            }
        );

        $event_dispatcher->addListener(
            'system.log',
            function (LogEvent $event) use ($command, $output) {
                $command->logEvent($output, $event);
            }
        );

        $event_dispatcher->addListener(
            'system.debug',
            function (DebugEvent $event) use ($command, $output) {
                $command->logEvent($output, $event);
            }
        );

        $already_connected = true;
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
        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $threshold = LogLevel::DEBUG;
        }

        if ($numericErrors[$event->getPriority()] >= $numericErrors[$threshold]) {
            /** @var Translator $translator  */
            $translator = $this->getContainer()->offsetGet('translator');
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
