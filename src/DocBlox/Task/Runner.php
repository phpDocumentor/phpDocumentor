<?php
/**
 * DocBlox
 *
 * @category  DocBlox
 * @package   Tasks
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 */

/**
 * This class is responsible for the execution of a task via the CLI.
 *
 * @category  DocBlox
 * @package   Tasks
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Task_Runner extends DocBlox_Core_Abstract
{
  /** @var DocBlox_Task_Abstract */
  protected $task = null;

  /**
   * Finds and initializes the given task.
   *
   * @param  $task_name
   */
  public function __construct($task_name)
  {
    // find the task which we want to use
    $task_parts = explode(':', $task_name);
    if (count($task_parts) == 1)
    {
      array_unshift($task_parts, 'project');
    }
    $class_name = 'DocBlox_Task_' . ucfirst($task_parts[0]) . '_' . ucfirst($task_parts[1]);

    // sorry about the shut up operator but we do this check to determine whether this works
    // and Zend_Loader throws a warning if the class does not exist.
    if (!@class_exists($class_name))
    {
      echo 'DocBlox version ' . DocBlox_Core_Abstract::VERSION . PHP_EOL . PHP_EOL;

      $this->log('Unable to execute task: ' . implode(':', $task_parts) . ', it is not found', DocBlox_Core_Log::CRIT);
      exit(1);
    }

    /** @var DocBlox_Task_Abstract $task  */
    $this->task = new $class_name();

    $this->task->parse(true);

    if (!$this->task->getQuiet())
    {
      echo 'DocBlox version ' . DocBlox_Core_Abstract::VERSION . PHP_EOL . PHP_EOL;
    }
  }

  /**
   * Returns the task to be ran.
   *
   * @return DocBlox_Task_Abstract|null
   */
  public function getTask()
  {
    return $this->task;
  }

  /**
   * Returns the version header.
   *
   * @return string
   */
  static public function renderVersion()
  {
    return 'DocBlox version ' . DocBlox_Core_Abstract::VERSION . PHP_EOL . PHP_EOL;
  }
}