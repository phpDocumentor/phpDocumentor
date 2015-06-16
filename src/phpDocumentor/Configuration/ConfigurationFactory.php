<?php

namespace phpDocumentor\Configuration;

use phpDocumentor\Uri;

final class ConfigurationFactory
{
    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    function __construct(Uri $uri)
    {
        $this->xml = $this->validate($uri);
    }

    public function convert()
    {
        $version = $this->checkIfVersionAttributeIsPresent($this->xml);
        if ($version) {
            $this->validateXmlStructure($this->xml);
        } else {
            $array = $this->convertPhpdoc2XmlToArray($this->xml);
        }

        return $array;
    }

    private function validate($uri)
    {
        $xml = new \SimpleXMLElement($uri, 0, true);

        if ($xml->getName() !== 'phpdocumentor') {
            throw new \InvalidArgumentException(sprintf('Root element name should be phpdocumentor, %s found', $xml->getName()));
        }

        return $xml;
    }

    /**
     * Checks if version attribute is present. If found, it is phpDocumentor3 configuration.
     * If no version attribute is found, it is assumed that it is phpDocumentor2 configuration.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return bool
     */
    private function checkIfVersionAttributeIsPresent($xml)
    {
        return isset($xml->attributes()->version);
    }

    private function convertPhpdoc2XmlToArray(\SimpleXMLElement $xml)
    {
        $array = [];
        foreach ($xml->children() as $children) {
            $array[] = $children;
        }

        return $array;
    }
}
