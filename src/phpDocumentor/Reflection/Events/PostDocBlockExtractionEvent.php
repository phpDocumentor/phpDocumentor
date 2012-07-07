<?php
namespace phpDocumentor\Reflection\Events;

class PostDocBlockExtractionEvent extends \phpDocumentor\Plugin\Event
{
    /** @var \phpDocumentor\Reflection\DocBlock */
    protected $docblock;

    /**
     * @param \phpDocumentor\Reflection\DocBlock $docblock
     */
    public function setDocblock($docblock)
    {
        $this->docblock = $docblock;
    }

    /**
     * @return \phpDocumentor\Reflection\DocBlock
     */
    public function getDocblock()
    {
        return $this->docblock;
    }
}
