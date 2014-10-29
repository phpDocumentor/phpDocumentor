<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract class representing the base elements of a phpDocumentor event.
 */
abstract class EventAbstract extends Event
{
    /** @var object Represents an object that is the subject of this event */
    protected $subject;

    /**
     * Initializes this event with the given subject.
     *
     * @param object $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the object that is the subject of this event.
     *
     * @return object
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Creates a new instance of a derived object and return that.
     *
     * Used as convenience method for fluent interfaces.
     *
     * @param object $subject
     *
     * @return static
     */
    public static function createInstance($subject)
    {
        return new static($subject);
    }
}
