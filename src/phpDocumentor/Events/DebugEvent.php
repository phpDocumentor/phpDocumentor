<?php
namespace phpDocumentor\Events;

class DebugEvent extends \phpDocumentor\Plugin\Event
{
    /**
     * @var string
     */
    protected $message;

    /**
     * Default priority level for these events is DEBUG
     *
     * @var int
     */
    protected $priority = \phpDocumentor\Plugin\Core\Log::DEBUG;

    /**
     * @param string $message
     *
     * @return DebugEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

}
