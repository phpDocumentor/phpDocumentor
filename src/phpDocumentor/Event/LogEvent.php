<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Event;

use Psr\Log\LogLevel;

/**
 * Logging event for phpDocumentor where information is output to the log or
 * stdout.
 */
class LogEvent extends DebugEvent
{
    /** @var string Default priority level for these events is INFO */
    protected $priority = LogLevel::INFO;

    /**
     * Set the priority level for this event.
     *
     * @see LogLevel for the constants used in determining the logging levels.
     */
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}
