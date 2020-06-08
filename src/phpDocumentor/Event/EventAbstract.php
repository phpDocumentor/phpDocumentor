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

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Abstract class representing the base elements of a phpDocumentor event.
 */
abstract class EventAbstract extends Event
{
    /** @var object Represents an object that is the subject of this event */
    protected $subject;

    /**
     * Initializes this event with the given subject.
     */
    public function __construct(object $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the object that is the subject of this event.
     */
    public function getSubject() : object
    {
        return $this->subject;
    }

    abstract public static function createInstance(object $subject) : self;
}
