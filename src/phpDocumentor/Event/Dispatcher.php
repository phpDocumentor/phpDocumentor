<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Event;

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
     * Returns a named instance of the Event Dispatcher.
     */
    public static function getInstance(string $name = 'default') : self
    {
        if (!isset(self::$instances[$name])) {
            self::setInstance($name, new self());
        }

        return self::$instances[$name];
    }

    /**
     * Sets a names instance of the Event Dispatcher.
     */
    public static function setInstance(string $name, self $instance) : void
    {
        self::$instances[$name] = $instance;
    }
}
