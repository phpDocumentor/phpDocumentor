<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Parser
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Parser;

/**
 * Layer superclass for \phpDocumentor\Parser Component.
 *
 * @category phpDocumentor
 * @package  Parser
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
abstract class ParserAbstract
{
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
        \phpDocumentor\Plugin\EventDispatcher::getInstance()->dispatch(
            'system.log',
            \phpDocumentor\Events\LogEvent::createInstance($this)
            ->setMessage($message)->setPriority($priority)
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
        \phpDocumentor\Plugin\EventDispatcher::getInstance()->dispatch(
            'system.debug',
            \phpDocumentor\Events\DebugEvent::createInstance($this)
            ->setMessage($message)
        );
    }
}
