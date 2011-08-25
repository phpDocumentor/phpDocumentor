<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

require_once 'markdown.php';
require_once 'symfony/components/event_dispatcher/lib/sfEventDispatcher.php';

/**
 * This class is responsible for the application entry point from the CLI.
 *
 * @category DocBlox
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @author   Ben Selby <benmatselby@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Core_Application
{
    /**
     * Main entry point into the application.
     *
     * @return void
     */
    public function main()
    {
        $runner = new DocBlox_Task_Runner(
            ($_SERVER['argc'] == 1)
            ? false
            : $_SERVER['argv'][1], 'project:run'
        );
        $task = $runner->getTask();

        $threshold = DocBlox_Core_Log::WARN;
        if (!$task->getQuiet()) {
            DocBlox_Core_Application::renderVersion();
        } else {
            $threshold = DocBlox_Core_Log::QUIET;
        }

        if ($task->getVerbose()) {
            $threshold = DocBlox_Core_Log::DEBUG;
        }

        $dispatcher = new sfEventDispatcher();

        $logger = new DocBlox_Core_Log(DocBlox_Core_Log::FILE_STDOUT);
        $logger->setThreshold($threshold);

        $dispatcher->connect('system.log', array($logger, 'log'));
        DocBlox_Parser_Abstract::$event_dispatcher      = $dispatcher;
        DocBlox_Transformer_Abstract::$event_dispatcher = $dispatcher;
        DocBlox_Reflection_Abstract::$event_dispatcher  = $dispatcher;

        try {
            $task->execute();
        } catch (Exception $e) {
            if (!$task->getQuiet()) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
                echo $task->getUsageMessage();
            }
            die(1);
        }
    }

    /**
     * Returns the version header.
     *
     * @return string
     */
    public static function renderVersion()
    {
        echo 'DocBlox version ' . DocBlox_Core_Abstract::VERSION
             . PHP_EOL
             . PHP_EOL;
    }
}
