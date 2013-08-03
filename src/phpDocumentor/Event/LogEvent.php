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

use Psr\Log\LogLevel;

/**
 * Logging event for phpDocumentor where information is output to the log or
 * stdout.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
class LogEvent extends DebugEvent
{
    /** @var int Default priority level for these events is INFO */
    protected $priority = LogLevel::INFO;

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
