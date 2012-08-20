<?php
namespace phpDocumentor\Reflection\Event;

class PostDocBlockExtractionEvent extends \phpDocumentor\Event\EventAbstract
{
    /** @var \phpDocumentor\Reflection\DocBlock */
    protected $docblock;

    /**
     * @param \phpDocumentor\Reflection\DocBlock $docblock
     */
    public function setDocblock($docblock)
    {
        $this->docblock = $docblock;
        return $this;
    }

    /**
     * @return \phpDocumentor\Reflection\DocBlock
     */
    public function getDocblock()
    {
        return $this->docblock;
    }
}
