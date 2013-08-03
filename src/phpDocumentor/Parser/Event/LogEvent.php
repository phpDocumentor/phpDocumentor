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
namespace phpDocumentor\Parser\Event;

/**
 * Event for capturing events during the parsing of a file.
 *
 * These events represent errors and warning found during processing of the file.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
class LogEvent extends \phpDocumentor\Event\EventAbstract
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $type;

    /** @var int */
    protected $code;

    /** @var int */
    protected $line;

    /**
     * Sets the numeric code for this event.
     *
     * Each parsing event should have a numeric code that does not overlap with
     * other errors. These codes and their messages are commonly found in the
     * Messages folder of your installed plugin.
     *
     * @param int $code
     *
     * @return LogEvent
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Returns the numeric code associated with this event.
     *
     * @see setCode for a complete description.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the line number where the event occurred.
     *
     * @param int $line
     *
     * @return LogEvent
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Returns the line number for this event.
     *
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Sets the message text for this event.
     *
     * @param string $message
     *
     * @see setCode() for more informetion regarding event codes and messages.
     *
     * @return LogEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Returns the message text associated with this event.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the type of event.
     *
     * @param string $type
     *
     * @todo find out which types are supported and expand this DocBlock.
     *
     * @return LogEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns the type for this event.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
