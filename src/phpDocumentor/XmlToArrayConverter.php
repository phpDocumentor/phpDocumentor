<?php

namespace phpDocumentor;

interface XmlToArrayConverter
{
    public function __construct(\SimpleXMLElement $xml);

    public function convert();
}
