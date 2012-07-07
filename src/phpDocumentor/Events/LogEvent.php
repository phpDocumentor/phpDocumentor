<?php
namespace phpDocumentor\Events;

class LogEvent extends DebugEvent
{
    /**
     * Default priority level for these events is INFO
     *
     * @var int
     */
    protected $priority = \phpDocumentor\Plugin\Core\Log::INFO;

    /**
     * @param int $priority
     *
     * @return LogEvent
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }
}
