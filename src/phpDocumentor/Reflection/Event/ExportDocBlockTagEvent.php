<?php
namespace phpDocumentor\Reflection\Event;

class ExportDocBlockTagEvent extends \phpDocumentor\Event\EventAbstract
{
    /** @var \SimpleXmlElement */
    protected $xml = null;

    /** @var \phpDocumentor\Reflection\DocBlock\Tag */
    protected $object = null;

    /**
     * @return \SimpleXmlElement|null
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @return \phpDocumentor\Reflection\DocBlock\Tag|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param $object
     * @return ExportDocBlockTagEvent
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @param $xml
     * @return ExportDocBlockTagEvent
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
        return $this;
    }
}
