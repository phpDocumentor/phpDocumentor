<?php

namespace phpDocumentor\ConfigurationFactory;

final class PhpDocumentor3 implements Strategy
{
    /**
     * @var string
     */
    private $schemaPath;

    /**
     * @param string $schemaPath
     */
    public function __construct($schemaPath)
    {
        $this->schemaPath = $schemaPath;
    }

    /**
     * Converts the phpDocumentor3 configuration xml to an array.
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @return array
     */
    public function convert(\SimpleXMLElement $phpDocumentor)
    {
        $this->validateXmlStructure($phpDocumentor);

        $versions = [];
        $template = [];

        foreach ($phpDocumentor->children() as $key => $value) {
            switch ((string) $key) {
                case 'version':
                    $versions[(string) $value->attributes()->number] = $this->buildVersions($value);
                    break;
                case 'template':
                    $template = $this->buildTemplate($value);
                    break;
                default:
                    break;
            }
        }

        $outputDirectory = ((string) $phpDocumentor->parser->target) ?: 'file://build/docs';
        $cacheDirectory  = ((string) $phpDocumentor->parser->cache) ?: '/tmp/phpdoc-doc-cache';

        $phpdoc3Array = [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => $outputDirectory,
                    'cache'  => $cacheDirectory,
                ],
                'versions'  => $versions,
                'templates' => $template,
            ],
        ];

        return $phpdoc3Array;
    }

    public function match()
    {
        return $this instanceof Strategy;
    }

    /**
     * Builds the versions part of the array from the phpDocumentor3 configuration xml.
     *
     * @param \SimpleXMLElement $version
     *
     * @return array
     */
    private function buildVersions(\SimpleXMLElement $version)
    {
        $extensions = [];
        foreach ($version->api->extensions->children() as $extension) {
            if ((string) $extension !== '') {
                $extensions[] = (string) $extension;
            }
        }

        $ignoreHidden   = filter_var($version->api->ignore->attributes()->hidden, FILTER_VALIDATE_BOOLEAN);
        $ignoreSymlinks = filter_var($version->api->ignore->attributes()->symlinks, FILTER_VALIDATE_BOOLEAN);

        return [
            'folder' => (string) $version->folder,
            'api'    => [
                'format'               => ((string) $version->api->attributes()->format) ?: 'php',
                'source'               => [
                    'dsn'   => (string) $version->api->source->attributes()->dsn,
                    'paths' => ((array) $version->api->source->path) ?: ['src'],
                ],
                'ignore'               => [
                    'hidden'   => $ignoreHidden,
                    'symlinks' => $ignoreSymlinks,
                    'paths'    => ((array) $version->api->ignore->path) ?: [],
                ],
                'extensions'           => $extensions,
                'visibility'           => (string) $version->api->visibility,
                'default-package-name' => ((string) $version->api->{'default-package-name'}) ?: 'Default',
                'markers'              => (array) $version->api->markers->children()->marker,
            ],
            'guide'  => [
                'format' => ((string) $version->guide->attributes()->format) ?: 'rst',
                'source' => [
                    'dsn'   => ((string) $version->guide->source->attributes()->dsn) ?: 'file://.',
                    'paths' => (array) $version->guide->source->path,
                ],
            ],
        ];
    }

    /**
     * Builds the template part of the array from the phpDocumentor3 configuration xml.
     *
     * @param \SimpleXMLElement $template
     *
     * @return array
     */
    private function buildTemplate(\SimpleXMLElement $template)
    {
        if (!$template) {
            // Use default template if none is found in the configuration
            return [
                [
                    'name' => 'clean',
                ],
            ];
        }

        $array = [];
        foreach ($template->attributes() as $key => $value) {
            $array[] = [
                (string) $key => (string) $value,
            ];
        }

        return $array;
    }

    /**
     * Validates the phpDocumentor3 xml structure against the schema defined in the schemaPath.
     *
     * @param $phpDocumentor
     *
     * @throws \InvalidArgumentException if the xml structure is not valid.
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
