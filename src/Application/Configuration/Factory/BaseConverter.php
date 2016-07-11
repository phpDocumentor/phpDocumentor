<?php

namespace phpDocumentor\Application\Configuration\Factory;

abstract class BaseConverter implements Converter
{
    /**
     * The path to the xsd that is used for validation of the configuration file.
     *
     * @var string
     */
    protected $schemaPath;

    /**
     * @var Converter
     */
    private $successor = null;

    /**
     * Initializes the converter.
     *
     * @param string $schemaPath
     */
    public function __construct($schemaPath)
    {
        $this->schemaPath = $schemaPath;
    }

    /**
     * Handles the request and/or redirect the request to the successor.
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @return mixed
     */
    abstract public function convert(\SimpleXMLElement $phpDocumentor);

    /**
     * Sets a successor converter.
     *
     * @param Converter $converter
     */
    public function setSuccessor(Converter $converter)
    {
        $this->successor = $converter;
    }
}
