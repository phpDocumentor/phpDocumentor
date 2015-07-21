<?php

namespace phpDocumentor\ConfigurationFactory;

interface Strategy
{
    public function convert(\SimpleXMLElement $xml);
}
