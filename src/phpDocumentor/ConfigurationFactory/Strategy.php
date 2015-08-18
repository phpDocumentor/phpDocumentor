<?php

namespace phpDocumentor\ConfigurationFactory;

interface Strategy
{
    public function convert(\SimpleXMLElement $xml);

    public function match(\SimpleXMLElement $xml);
}
