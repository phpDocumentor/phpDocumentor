<?php

namespace phpDocumentor\Application\Configuration\Factory;

interface Converter
{
    /**
     * @param \SimpleXMLElement $xml
     *
     * @return \SimpleXMLElement
     */
    public function convert(\SimpleXMLElement $xml);
}
