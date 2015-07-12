<?php

namespace phpDocumentor;

final class Phpdoc3XmlToArrayConverter implements XmlConverter
{
    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    /**
     * Converts the phpDocumentor3 configuration xml to an array.
     *
     * @return array
     */
    public function convert()
    {
        $phpDocumentor = $this->xml;

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
            ]
        ];

        return $phpdoc3Array;
    }

    /**
     * Builds the versions part of the array from the phpDocumentor3 configuration xml
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
                    'hidden'   => ($ignoreHidden) ?: true,
                    'symlinks' => ($ignoreSymlinks) ?: true,
                    'paths'    => ((array) $version->api->ignore->path) ?: ['src/ServiceDefinitions.php'],
                ],
                'extensions'           => $extensions,
                'visibility'           => (string) $version->api->visibility,
                'default-package-name' => ((string) $version->api->{'default-package-name'}) ?: 'Default',
                'markers'              => (array) $version->api->markers->children()->marker,
            ],
            'guide'  => [
                'format' => ((string) $version->guide->attributes()->format) ?: 'rst',
                'source' => [
                    'dsn'   => ((string) $version->guide->source->attributes()->dsn) ?: 'file://../phpDocumentor/phpDocumentor3',
                    'paths' => (array) $version->guide->source->path,
                ]
            ]
        ];
    }

    /**
     * Builds the template part of the array from the phpDocumentor3 configuration xml
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
                0 => [
                    'name' => 'clean'
                ],
                1 => [
                    'location' => 'https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean'
                ]
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
}
