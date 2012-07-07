<?php
namespace phpDocumentor\Parser\Events;

class PreFileEvent extends \phpDocumentor\Plugin\Event
{
    /** @var string */
    protected $file;

    /** @var int[] */
    protected $progress = array(0,0);

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param int[] $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
        return $this;
    }

    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }
}
