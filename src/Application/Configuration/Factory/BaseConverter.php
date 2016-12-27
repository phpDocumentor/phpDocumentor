<?php

namespace phpDocumentor\Application\Configuration\Factory;

abstract class BaseConverter implements Converter
{
    /**
     * @var Converter|null
     */
    protected $nextConverter;

    abstract public function __construct(Converter $converter = null);

    abstract protected function innerConvert(\SimpleXMLElement $xml);

    abstract protected function match(\SimpleXMLElement $xml);

    /**
     * @inheritdoc
     */
    public function convert(\SimpleXMLElement $xml)
    {
        if (!$this->match($xml)) {
             $xml = $this->nextConverter->convert($xml);
        }

        return $this->innerConvert($xml);
    }
}
