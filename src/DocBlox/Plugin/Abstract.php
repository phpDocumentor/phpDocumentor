<?php
class DocBlox_Plugin_Abstract
{
    protected $event_dispatcher = null;
    protected $configuration = null;

    function __construct($event_dispatcher, $configuration)
    {
        $this->event_dispatcher = $event_dispatcher;
        $this->configuration = $configuration;
    }

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
        if (!$this->event_dispatcher) {
            return null;
        }

        if (!$this->event_dispatcher instanceof sfEventDispatcher) {
            throw new DocBlox_Parser_Exception(
                'Expected the event dispatcher to be an instance of '
                . 'sfEventDispatcher'
            );
        }

        $event = $this->event_dispatcher->notify(
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
                 'message' => $message,
                 'priority' => $priority
            )
        );
    }

    /**
     * Dispatches a parser error to be logged.
     *
     * @param string $type The logging priority as string
     * @param string $message  The message to log.
     * @param string $line     The line number where the error occurred..
     *
     * @return void
     */
    public function logParserError($type, $message, $line)
    {
        $this->log($message, DocBlox_Core_Log::ERR);
        $this->dispatch(
            'parser.log',
            array(
                 'type' => $type,
                 'message' => $message,
                 'line' => $line
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
            array('message' => $message)
        );
    }

}
