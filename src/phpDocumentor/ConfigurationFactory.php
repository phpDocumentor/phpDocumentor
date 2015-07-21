<?php

namespace phpDocumentor;

use phpDocumentor\ConfigurationFactory\PhpDocumentor2;
use phpDocumentor\ConfigurationFactory\PhpDocumentor3;

final class ConfigurationFactory
{
    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * @var string
     */
    private $schemaPath;

    /**
     * @param Uri    $uri
     * @param string $schemaPath
     */
    public function __construct(Uri $uri, $schemaPath = '')
    {
        if ($schemaPath === '') {
            $schemaPath = __DIR__ . '/../../data/xsd/phpdoc.xsd';
        }

        $this->schemaPath = $schemaPath;

        $this->replaceLocation($uri);
    }

    /**
     * Replaces the location of the configuration file if it is different.
     *
     * @param Uri $uri
     */
    public function replaceLocation(Uri $uri)
    {
        if ($this->uri !== $uri) {
            $this->xml = new \SimpleXMLElement($uri, 0, true);

            $this->uri = $uri;
        }
    }

    /**
     * Converts the phpDocumentor configuration xml to an array.
     *
     * @return array
     */
    public function get()
    {
        $this->validate($this->xml);

        $version = $this->checkIfVersionAttributeIsPresent($this->xml);
        if ($version) {
            $xml = new PhpDocumentor3($this->schemaPath);
        } else {
            $xml = new PhpDocumentor2();
        }

        $array = $xml->convert($this->xml);

        return $array;
    }

    /**
     * Validates if the xml has a root element which name is phpdocumentor.
     *
     * @param \SimpleXMLElement $xml
     *
     * @throws \InvalidArgumentException if the root element of the xml is not phpdocumentor.
     */
    private function validate(\SimpleXMLElement $xml)
    {
        if ($xml->getName() !== 'phpdocumentor') {
            throw new \InvalidArgumentException(sprintf('Root element name should be phpdocumentor, %s found',
                $xml->getName()));
        }
    }

    /**
     * Checks if version attribute is present. If found, it is phpDocumentor3 configuration.
     * If no version attribute is found, it is assumed that it is phpDocumentor2 configuration.
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @return bool
     */
    private function checkIfVersionAttributeIsPresent(\SimpleXMLElement $phpDocumentor)
    {
        return isset($phpDocumentor->attributes()->version);
    }
}
