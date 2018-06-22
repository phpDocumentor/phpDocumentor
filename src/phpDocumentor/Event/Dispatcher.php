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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Event Dispatching class.
 *
 * This class provides a bridge to the Symfony2 EventDispatcher.
 * At current this is provided by inheritance but future iterations should
 * solve this by making it an adapter pattern.
 *
 * The class is implemented as (mockable) Singleton as this was the best
 * solution to make the functionality available in every class of the project.
 */
class Dispatcher extends EventDispatcher
{
    /** @var Dispatcher[] Keep track of an array of instances. */
    protected static $instances = [];

    /**
     * Override constructor to make this singleton.
     * @codeCoverageIgnore For some reason
     */
    protected function __construct()
    {
    }

    /**
     * Returns a named instance of the Event Dispatcher.
     */
    public static function getInstance(string $name = 'default'): self
    {
        if (!isset(self::$instances[$name])) {
            self::setInstance($name, new self());
        }

        return self::$instances[$name];
    }

    /**
     * Sets a names instance of the Event Dispatcher.
     */
    public static function setInstance(string $name, self $instance): void
    {
        self::$instances[$name] = $instance;
    }

    /**
     * Dispatches an event.
     *
     * Please note that the typehint of this method indicates a Symfony Event
     * and this DocBlock a phpDocumentor event. This is because of inheritance
     * and that the dispatch signature must remain intact.
     *
     * @param string $eventName
     * @codeCoverageIgnore Untestable and not really necessary
     * @return EventAbstract
     */
    public function dispatch($eventName, Event $event = null)
    {
        return parent::dispatch($eventName, $event);
    }

    /**
     * Adds a callable that will listen on the named event.
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     *
     * @codeCoverageIgnore Untestable and not really necessary
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        parent::addListener($eventName, $listener, $priority);
    }
}
