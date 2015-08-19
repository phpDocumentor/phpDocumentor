<?php

namespace phpDocumentor;

use phpDocumentor\ConfigurationFactory\PhpDocumentor2;
use phpDocumentor\ConfigurationFactory\PhpDocumentor3;
use phpDocumentor\ConfigurationFactory\Strategy;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 */
final class ConfigurationFactory
{
    /**
     * The Uri that contains the path to the configuration file.
     *
     * @var Uri
     */
    private $uri;

    /**
     * The configuration xml.
     *
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * All strategies that are used by the ConfigurationFactory.
     *
     * @var Strategy[]
     */
    private $strategies = [];

    /**
     * Initializes the ConfigurationFactory.
     *
     * @param Uri    $uri
     * @param string $schemaPath
     */
    public function __construct(Uri $uri, $schemaPath = '')
    {
        // @todo: strategies should be constructor arguments, but that requires updating all tests.
        $strategies = [
            new PhpDocumentor2(),
            new PhpDocumentor3($schemaPath),
        ];

        $this->replaceLocation($uri);

        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }
    }

    /**
     * Replaces the location of the configuration file if it differs from the existing one.
     *
     * @param Uri $uri
     *
     * @return void
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
     * @throws \RuntimeException if no matching strategy can be found.
     */
    public function get()
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->match($this->xml) === true) {
                return $strategy->convert($this->xml);
            }
        };

        throw new \RuntimeException('No strategy found that matches the configuration xml');
    }

    /**
     * Adds strategies that are used in the ConfigurationFactory.
     *
     * @param Strategy $strategy
     *
     * @return void
     */
    private function registerStrategy(Strategy $strategy)
    {
        $this->strategies[] = $strategy;
    }
}
