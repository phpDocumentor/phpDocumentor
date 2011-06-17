<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * This class is responsible for the application entry point from the CLI.
 *
 * @category   DocBlox
 * @package    Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @author     Ben Selby <benmatselby@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
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
    require_once 'markdown.php';

    $runner = new DocBlox_Task_Runner(($_SERVER['argc'] == 1) ? false : $_SERVER['argv'][1], 'project:run');
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
