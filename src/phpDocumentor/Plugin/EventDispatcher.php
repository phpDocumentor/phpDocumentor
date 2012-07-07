<?php
namespace phpDocumentor\Plugin;

use Symfony\Component\EventDispatcher as Symfony;

class EventDispatcher extends Symfony\EventDispatcher
{
    static protected $instances = array();

    protected function __construct()
    {
    }

    /**
     * Returns a named instance of the Event Dispatcher.
     *
     * @param string $name
     *
     * @return EventDispatcher
     */
    public static function getInstance($name = 'default')
    {
        if (!isset(self::$instances[$name])) {
            self::setInstance($name, new self());
        }

        return self::$instances[$name];
    }

    public static function setInstance($name, self $instance)
    {
        self::$instances[$name] = $instance;
    }

    public function dispatch($eventName, \Symfony\Component\EventDispatcher\Event $event = null)
    {
        return parent::dispatch($eventName, $event);
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        parent::addListener($eventName, $listener, $priority);
    }

    /**
     * @param $eventName
     * @param $listener
     *
     * @deprecated provided for BC compatibility; use addListener instead.
     *     Will be removed in 2.0.0
     */
    public function connect($eventName, $listener)
    {
        trigger_error(
            'The method "connect" will be removed in 2.0.0', E_USER_DEPRECATED
        );
        $this->addListener($eventName, $listener);
    }
}
