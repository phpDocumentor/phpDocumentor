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

use Psr\Log\LogLevel;

/**
 * Event representing some debugging information for phpDocumentor.
 *
 * The information logged by this event will only be visible if the verbose
 * option is provided to phpDocumentor.
 */
class DebugEvent extends EventAbstract
{
    /** @var string Message to display with the debugging event */
    protected $message;

    /** @var int Default priority level for these events is DEBUG */
    protected $priority = LogLevel::DEBUG;

    /** @var string[] Extra parameters to insert into the message after translation */
    protected $context = array();

    /**
     * Provides the message that is to be shown with this event.
     *
     * @param string $message
     *
     * @return DebugEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Returns the message that was provided with this event.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns the priority level associated with this logging event.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets additional context (parameters) to use when translating messages.
     *
     * @param string[] $context
     *
     * @return self
     */
    public function setContext(array $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Returns the context for this event.
     *
     * @return string[]
     */
    public function getContext()
    {
        return $this->context;
    }
}
