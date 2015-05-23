<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Event;

/**
 * Logging event for phpDocumentor where information is output to stdout.
 */
class LogEvent extends DebugEvent
{
    /** @var int Default priority level for these events is INFO */
    protected $priority = 'info';

    /**
     * Set the priority level for this event.
     *
     * @param int $priority
     *
     * @see LogLevel for the constants used in determining the logging levels.
     *
     * @return LogEvent
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }
}
