<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Tasks
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * This class is responsible for the execution of a task.
 *
 * @category   DocBlox
 * @package    Tasks
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Task_Runner extends DocBlox_Core_Abstract
{
  /** @var DocBlox_Task_Abstract */
  protected $task = null;

  /**
   * Finds and initializes the given task.
   *
   * @param string $task_name Name of the task to prepare for running.
   * @param string $default   Task to fall back on if no task could be resolved.
   */
  public function __construct($task_name, $default)
  {
    $task_parts = explode(':', $default);

    if ($task_name && strpos($task_name, '-') === false) {
      // find the task which we want to use
      $task_parts = explode(':', $task_name);

      if (count($task_parts) == 1)
      {
        array_unshift($task_parts, 'project');
      }
    }

    $class_name = 'DocBlox_Task_' . ucfirst($task_parts[0]) . '_' . ucfirst($task_parts[1]);

    // sorry about the shut up operator but we do this check to determine whether this works
    // and Zend_Loader throws a warning if the class does not exist.
    if (!@class_exists($class_name))
    {
      $this->log('Unable to execute task: ' . implode(':', $task_parts) . ', it is not found', DocBlox_Core_Log::CRIT);
      exit(1);
    }

    /** @var DocBlox_Task_Abstract $task  */
    $this->task = new $class_name();
    $this->task->parse(true);
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

}