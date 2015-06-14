<?php

namespace phpDocumentor\Configuration;

use phpDocumentor\Uri;

final class ConfigurationFactory
{
    function __construct(Uri $uri)
    {
        $this->checkIfUriIsReadable($uri);
        $this->checkIfUriIsAFile($uri);
        $this->CheckIfUriHasContent($uri);
        $this->CheckIfUriContainsWellFormedXml($uri);
        $xml     = $this->CheckIfRootElementNameIsPhpdocumentor($uri);
        $version = $this->checkIfVersionAttributeIsPresent($xml);
        if (!$version) {
            $array = $this->convertPhpdoc2XmlToArray($xml);
        }

        return $array;
    }

    private function checkIfUriIsReadable($uri)
    {
        if (!is_readable($uri)) {
            throw new \InvalidArgumentException('Uri is not readable');
        }
    }

    private function checkIfUriIsAFile($uri)
    {
        if (!is_file($uri)) {
            throw new \InvalidArgumentException('Uri is not a file');
        }
    }

    private function CheckIfUriHasContent($uri)
    {
        if (file_get_contents($uri) === '') {
            throw new \InvalidArgumentException('Uri has empty content');
        }
    }

    private function CheckIfUriContainsWellFormedXml($uri)
    {
        libxml_clear_errors();
        libxml_use_internal_errors(true);
        simplexml_load_file($uri);
        $error = libxml_get_last_error();
        if ($error) {
            throw new \InvalidArgumentException('Uri does not contain well-formed xml');
        }
    }

    private function CheckIfRootElementNameIsPhpdocumentor($uri)
    {
        $xml = new \SimpleXMLElement($uri, 0, true);
        if ($xml->getName() !== 'phpdocumentor') {
            throw new \InvalidArgumentException(sprintf('Root element name should be phpdocumentor, %s found', $xml->getName()));
        }

        return $xml;
    }

    /**
     * Checks if version attribute is present. If found, it is phpDocumentor3 configuration.
     * If not found, it is assumed that it is phpDocumentor2 configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return bool
     */
    private function checkIfVersionAttributeIsPresent(\SimpleXMLElement $xml)
    {
        return isset($xml->attributes()->version);
    }

    private function convertPhpdoc2XmlToArray(\SimpleXMLElement $xml)
    {
        $json  = json_encode($xml);
        $array = json_decode($json, true);

        return $array;
    }
}
