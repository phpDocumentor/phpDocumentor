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
namespace phpDocumentor\Parser;

use phpDocumentor\Plugin\EventDispatcher;
use phpDocumentor\Events\LogEvent;
use phpDocumentor\Events\DebugEvent;

/**
 * Layer superclass for \phpDocumentor\Parser Component.
 */
abstract class ParserAbstract
{
    /**
     * Dispatches a logging request.
     *
     * @param string $message  The message to log.
     * @param int    $priority The logging priority, the lower,the more
     *     important. Ranges from 1 to 7
     *
     * @return void
     */
    public function log($message, $priority = 6)
    {
        EventDispatcher::getInstance()->dispatch(
            'system.log',
            LogEvent::createInstance($this)
                ->setMessage($message)
                ->setPriority($priority)
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
        EventDispatcher::getInstance()->dispatch(
            'system.debug',
            DebugEvent::createInstance($this)->setMessage($message)
        );
    }
}
