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

/**
 * Abstract class representing the base elements of a phpDocumentor event.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class EventAbstract extends \Symfony\Component\EventDispatcher\Event implements \ArrayAccess
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
     * @return EventAbstract
     */
    public static function createInstance($subject)
    {
        return new static($subject);
    }

    /**
     * Convenience method to return properties of this event.
     *
     * Prior to the refactoring to Symfony2's EventDispatcher there were no
     * clearly defined event classes and was properties passed using array
     * elements.
     *
     * Since 2.0.0a8 we now use explicit properties and getters and setter for
     * this as it makes code more readable and the event writer more flexible.
     *
     * This method triggers an E_USER_DEPRECATED and should be removed when
     * going Beta or RC.
     *
     * @param string $offset
     *
     * @deprecated should be removed in 2.0.0 and getters should be used
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        trigger_error(
            'Calling event attributes as array elements is deprecated since '
            .'2.0.0a8, please use the Event object\'s getters as this feature '
            .'will be removed in 2.0.0',
            E_USER_DEPRECATED
        );

        $offset = str_replace('_', ' ', $offset);
        $offset = 'get'.str_replace(' ', '', ucwords($offset));

        return $this->$offset();
    }

    /**
     * Requirement of the ArrayAccess interface; we do not implement it as it is
     * deprecated.
     *
     * @param string $offset
     *
     * @throws \Exception because method is explicitly not implemented.
     *
     * @deprecated should be removed in 2.0.0 beta or rc
     *
     * @return void
     */
    public function offsetExists($offset)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Requirement of the ArrayAccess interface; we do not implement it as it is
     * deprecated.
     *
     * @param string $offset
     * @param string $value
     *
     * @throws \Exception because method is explicitly not implemented.
     *
     * @deprecated should be removed in 2.0.0 beta or rc
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Requirement of the ArrayAccess interface; we do not implement it as it is
     * deprecated.
     *
     * @param string $offset
     *
     * @throws \Exception because method is explicitly not implemented.
     *
     * @deprecated should be removed in 2.0.0 beta or rc
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('Not implemented');
    }
}
