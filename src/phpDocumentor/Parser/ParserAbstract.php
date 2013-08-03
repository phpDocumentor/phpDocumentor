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

use Psr\Log\LogLevel;
use phpDocumentor\Event\DebugEvent;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;

/**
 * Layer superclass for \phpDocumentor\Parser Component.
 */
abstract class ParserAbstract
{
    /**
     * Dispatches a logging request.
     *
     * @param string $message  The message to log.
     * @param int    $priority The logging priority as declared in the LogLevel PSR-3 class.
     *
     * @return void
     */
    public function log($message, $priority = LogLevel::INFO, $parameters = array())
    {
        Dispatcher::getInstance()->dispatch(
            'system.log',
            LogEvent::createInstance($this)
                ->setContext($parameters)
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
        Dispatcher::getInstance()->dispatch(
            'system.debug',
            DebugEvent::createInstance($this)->setMessage($message)
        );
    }
}
