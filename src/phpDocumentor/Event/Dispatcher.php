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

namespace phpDocumentor\Event;

use Symfony\Component\EventDispatcher as Symfony;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event Dispatching class.
 *
 * This class provides a bridge to the Symfony2 EventDispatcher.
 * At current this is provided by inheritance but future iterations should
 * solve this by making it an adapter pattern.
 *
 * The class is implemented as (mockable) Singleton as this was the best
 * solution to make the functionality available in every class of the project.
 */
class Dispatcher extends Symfony\EventDispatcher
{
    /** @var Dispatcher[] Keep track of an array of instances. */
    protected static $instances = array();

    /**
     * Override constructor to make this singleton.
     * @codeCoverageIgnore For some reason
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
     * @param Event  $event
     *
     * @codeCoverageIgnore Untestable and not really necessary
     *
     * @return EventAbstract
     */
    public function dispatch($eventName, Event $event = null)
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
     * @codeCoverageIgnore Untestable and not really necessary
     *
     * @return void
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        parent::addListener($eventName, $listener, $priority);
    }
}
