<?php
namespace phpDocumentor\Transformer\Events;

class PreTransformationEvent extends \phpDocumentor\Plugin\Event
{
    /** @var \DOMDocument */
    protected $source;

    /**
     * @param \DOMDocument $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return \DOMDocument
     */
    public function getSource()
    {
        return $this->source;
    }
}
