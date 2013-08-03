<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Event;

use Symfony\Component\EventDispatcher as Symfony;

/**
 * Event Dispatching class.
 *
 * This class provides a bridge to the Symfony2 EventDispatcher.
 * At current this is provided by inheritance but future iterations should
 * solve this by making it an adapter pattern.
 *
 * The class is implemented as (mockable) Singleton as this was the best
 * solution to make the functionality available in every class of the project.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
class Dispatcher extends Symfony\EventDispatcher
{
    /** @var Dispatcher[] Keep track of an array of instances. */
    protected static $instances = array();

    /**
     * Override constructor to make this singleton.
     */
    protected function __construct()
    {
    }

    /**
     * Returns a named instance of the Event Dispatcher.
     *
     * @param string $name
     *
     * @return Dispatcher
     */
    public static function getInstance($name = 'default')
    {
        if (!isset(self::$instances[$name])) {
            self::setInstance($name, new self());
        }

        return self::$instances[$name];
    }

    /**
     * Sets a names instance of the Event Dispatcher.
     *
     * @param string     $name
     * @param Dispatcher $instance
     *
     * @return void
     */
    public static function setInstance($name, self $instance)
    {
        self::$instances[$name] = $instance;
    }

    /**
     * Dispatches an event.
     *
     * Please note that the typehint of this method indicates a Symfony Event
     * and this DocBlock a phpDocumentor event. This is because of inheritance
     * and that the dispatch signature must remain intact.
     *
     * @param string $eventName
     * @param EventAbstract $event
     *
     * @return EventAbstract
     */
    public function dispatch($eventName, Symfony\Event $event = null)
    {
        return parent::dispatch($eventName, $event);
    }

    /**
     * Adds a callable that will listen on the named event.
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     *
     * @return void
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        parent::addListener($eventName, $listener, $priority);
    }
}
