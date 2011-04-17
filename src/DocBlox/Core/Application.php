<?php
/**
 * DocBlox
 *
 * @category  DocBlox
 * @package   Core
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 */

/**
 * This class is responsible for the application entry point from the cli
 *
 * @category  DocBlox
 * @package   Tasks
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Core_Application
{
  /**
   * Main entry point into the application
   *
   * @return void
   */
  public function main()
  {
    require_once 'Image/GraphViz.php';
    require_once 'markdown.php';

    $runner = new DocBlox_Task_Runner(($_SERVER['argc'] == 1) ? 'project:run' : $_SERVER['argv'][1]);
    $task = $runner->getTask();

    if (!$task->getQuiet())
    {
      DocBlox_Core_Application::renderVersion();
    }

    try
    {
      $task->execute();
    }
    catch(Exception $e)
    {
      echo 'ERROR: '.$e->getMessage().PHP_EOL.PHP_EOL;
      echo $task->getUsageMessage();
    }
  }

  /**
   * Returns the version header.
   *
   * @return string
   */
  public static function renderVersion()
  {
    echo 'DocBlox version ' . DocBlox_Core_Abstract::VERSION . PHP_EOL . PHP_EOL;
  }
}