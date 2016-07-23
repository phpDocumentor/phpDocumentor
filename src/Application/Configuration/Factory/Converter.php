<?php

namespace phpDocumentor\Application\Configuration\Factory;

interface Converter
{
    public function convertToLatestVersion(\SimpleXMLElement $xml);
}
