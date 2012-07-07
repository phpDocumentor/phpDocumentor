<?php
namespace phpDocumentor\Plugin;

abstract class Event extends \Symfony\Component\EventDispatcher\Event
    implements \ArrayAccess
{
    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    /**
     *
     *
     * @param object $subject
     *
     * @return Event
     */
    public static function createInstance($subject)
    {
        return new static($subject);
    }

    /**
     * @param mixed $offset
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
            .'will be removed in 2.0.0', E_USER_DEPRECATED
        );

        $offset = str_replace('_', ' ', $offset);
        $offset = 'get'.str_replace(' ', '', ucwords($offset));
        return $this->$offset();
    }

    public function offsetExists($offset)
    {
        throw new \Exception('Not implemented');
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception('Not implemented');
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Not implemented');
    }
}
