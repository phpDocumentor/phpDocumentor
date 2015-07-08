<?php

namespace phpDocumentor;

final class ConfigurationFactory
{
    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var string
     */
    private $schemaPath;

    /**
     * @var bool
     */
    private $validateUri;

    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * @param Uri    $uri
     * @param string $schemaPath
     */
    public function __construct(Uri $uri, $schemaPath = '')
    {
        if ($schemaPath === '') {
            $schemaPath = __DIR__ . '/../../data/xsd/phpdoc.xsd';
        }

        $this->replaceLocation($uri);
        $this->schemaPath = $schemaPath;
    }

    /**
     * Replaces the location of the configuration file if it is different.
     *
     * @param Uri $uri
     */
    public function replaceLocation(Uri $uri)
    {
        if ($this->uri !== $uri) {
            $this->validateUri = true;
        }

        $this->uri = $uri;
    }

    /**
     * Converts the phpDocumentor configuration xml to an array.
     *
     * @return array
     */
    public function get()
    {
        $this->validate($this->uri);

        $version = $this->checkIfVersionAttributeIsPresent($this->xml);
        if ($version) {
            $this->validateXmlStructure($this->xml);
            $xml = new Phpdoc3XmlToArrayConverter($this->xml);
            $array = $xml->convert();
        } else {
            $xml = new Phpdoc2XmlToArrayConverter($this->xml);
            $array = $xml->convert();
        }

        return $array;
    }

    /**
     * Validates if the Uri contains an xml that has a root element which name is phpdocumentor.
     *
     * @param Uri $uri
     *
     * @throws \InvalidArgumentException if the root element of the xml is not phpdocumentor.
     */
    private function validate(Uri $uri)
    {
        if ($this->validateUri === false) {
            return;
        }

        $xml = new \SimpleXMLElement($uri, 0, true);

        if ($xml->getName() !== 'phpdocumentor') {
            throw new \InvalidArgumentException(sprintf('Root element name should be phpdocumentor, %s found',
                $xml->getName()));
        }

        $this->xml = $xml;

        $this->validateUri = false;
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

    /**
     * Validates the phpDocumentor3 xml structure against phpdoc.xsd
     *
     * @param $phpDocumentor
     */
    private function validateXmlStructure(\SimpleXMLElement $phpDocumentor)
    {
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        $dom        = new \DOMDocument();
        $domElement = dom_import_simplexml($phpDocumentor);
        $domElement = $dom->importNode($domElement, true);
        $dom->appendChild($domElement);

        $dom->schemaValidate($this->schemaPath);

        $error = libxml_get_last_error();

        if ($error) {
            throw new \InvalidArgumentException($error->message);
        }
    }
}
