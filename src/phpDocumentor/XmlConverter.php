<?php

namespace phpDocumentor;

interface XmlConverter
{
    public function __construct(\SimpleXMLElement $xml);

    public function convert();
}
