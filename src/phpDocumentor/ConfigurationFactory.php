<?php

namespace phpDocumentor;

use phpDocumentor\ConfigurationFactory\PhpDocumentor2;
use phpDocumentor\ConfigurationFactory\PhpDocumentor3;
use phpDocumentor\ConfigurationFactory\Strategy;

final class ConfigurationFactory
{
    /**
     * The Uri that contains the path to the configuration file
     *
     * @var Uri
     */
    private $uri;

    /**
     * The configuration xml
     *
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * The path to the xsd that is used for validation of the configuration file.
     *
     * @var string
     */
    private $schemaPath;

    /**
     * All strategies that are used by the ConfigurationFactory
     *
     * @var Strategy[]
     */
    private $strategies;

    /**
     * Initializes the ConfigurationFactory.
     *
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

        $this->registerStrategies();
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
     *
     * @throws \RuntimeException if no strategy can be found.
     */
    public function get()
    {
        $this->validate($this->xml);

        foreach ($this->strategies as $strategy) {
            if ($strategy->match($this->xml) === true) {
                return $strategy->convert($this->xml);
            }
        };

        throw new \RuntimeException('No strategy found that matches the configuration xml');
    }

    /**
     * Registers strategies that are used in the ConfigurationFactory.
     *
     * @return void
     */
    private function registerStrategies()
    {
        $this->strategies = [
            new PhpDocumentor2(),
            new PhpDocumentor3($this->schemaPath),
        ];
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
}
