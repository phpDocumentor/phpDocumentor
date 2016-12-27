<?php

namespace phpDocumentor\Application\Configuration\Factory;

interface Extractor
{
    public function extract(\SimpleXMLElement $xml);
}
