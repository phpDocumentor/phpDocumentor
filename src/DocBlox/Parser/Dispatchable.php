<?php
include_once('sfEventDispatcher.php');

/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Parser
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * This interface tells that a class is capable of invoking events on the given event dispatcher.
 *
 * @category DocBlox
 * @package  Parser
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
interface DocBlox_Parser_Dispatchable
{

    /**
     * Sets the event dispatcher for this class.
     *
     * @param mixed $dispatcher
     *
     * @return void
     */
    public function setEventDispatcher($dispatcher);

    /**
     * Notifies the event dispatcher of the given event ($name) and $arguments and
     * returns the result of the notification.
     *
     * @param string  $name    Name of the invoked event.
     * @param array $arguments Associative array with arguments for this event.
     *
     * @return mixed
     */
    public function notify($name, array $arguments);
}
