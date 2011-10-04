<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Transformer
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Layer superclass for DocBlox_Transformer Component.
 *
 * @category DocBlox
 * @package  Transformer
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
abstract class DocBlox_Transformer_Abstract
{
    /**
     * The event dispatcher object, may be null to not dispatch events.
     *
     * @var sfEventDispatcher|null
     */
    public static $event_dispatcher = null;

    /**
     * Dispatches an event to the Event Dispatcher.
     *
     * This method tries to dispatch an event; if no Event Dispatcher has been
     * set than this method will explicitly not fail and return null.
     *
     * By not failing we make the Event Dispatcher optional and is it easier
     * for people to re-use this component in their own application.
     *
     * @param string   $name      Name of the event to dispatch.
     * @param string[] $arguments Arguments for this event.
     *
     * @throws DocBlox_Parser_Exception if there is a dispatcher but it is not
     *  of type sfEventDispatcher
     *
     * @return mixed|null
     */
    public function dispatch($name, $arguments)
    {
        if (!self::$event_dispatcher) {
            return null;
        }

        if (!self::$event_dispatcher instanceof sfEventDispatcher) {
            throw new DocBlox_Parser_Exception(
                'Expected the event dispatcher to be an instance of '
                . 'sfEventDispatcher'
            );
        }

        $event = self::$event_dispatcher->notify(
            new sfEvent($this, $name, $arguments)
        );

        return $event
            ? $event->getReturnValue()
            : null;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $message  The message to log.
     * @param int    $priority The logging priority, the lower,
     *  the more important. Ranges from 1 to 7
     *
     * @return void
     */
    public function log($message, $priority = 6)
    {
        $this->dispatch(
            'system.log',
            array(
                'message'  => $message,
                'priority' => $priority
            )
        );
    }

    /**
     * Dispatches a logging request to log a debug message.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    public function debug($message)
    {
        $this->dispatch(
            'system.debug',
            array('message'  => $message)
        );
    }
}
