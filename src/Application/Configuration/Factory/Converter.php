<?php

namespace phpDocumentor\Application\Configuration\Factory;

interface Converter
{
    public function convert(\SimpleXMLElement $xml);
}
