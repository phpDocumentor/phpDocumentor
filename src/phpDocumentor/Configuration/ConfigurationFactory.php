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
        $extensions         = [];
        $markers            = [];
        $visibility         = 'public';
        $defaultPackageName = 'Default';
        $template           = 'clean';
        $ignoreHidden       = true;
        $ignoreSymlinks     = true;

        if (isset($xml->parser)) {
            if (isset($xml->parser->extensions)) {
                foreach ($xml->parser->extensions->children() as $extension) {
                    $extensions[] = (string) $extension;
                }
            }

            if (isset($xml->parser->markers)) {
                foreach ($xml->parser->markers->children() as $marker) {
                    $markers[] = (string) $marker;
                }
            }

            $visibility         = ((string) $xml->parser->visibility) ?: $visibility;
            $defaultPackageName = ((string) $xml->parser->{'default-package-name'}) ?: $defaultPackageName;
            $template           = ((string) $xml->transformations->template->attributes()->name) ?: $template;

            if (isset($xml->parser->files)) {
                if (isset($xml->parser->files->{'ignore-hidden'})) {
                    $ignoreHidden = filter_var($xml->parser->files->{'ignore-hidden'}, FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($xml->parser->files->{'ignore-symlinks'})) {
                    $ignoreSymlinks = filter_var($xml->parser->files->{'ignore-symlinks'}, FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        $phpdoc2Array = [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => 'file://build/docs',
                    'cache'  => '/tmp/phpdoc-doc-cache'
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src'
                                ]
                            ],
                            'ignore'               => [
                                'hidden'   => $ignoreHidden,
                                'symlinks' => $ignoreSymlinks,
                                'paths'    => [
                                    0 => 'src/ServiceDefinitions.php'
                                ]
                            ],
                            'extensions'           => $extensions,
                            'visibility'           => $visibility,
                            'default-package-name' => $defaultPackageName,
                            'markers'              => $markers,
                        ],
                        'guide'  => [
                            'format' => 'rst',
                            'source' => [
                                'dsn'   => 'file://../phpDocumentor/phpDocumentor2',
                                'paths' => [
                                    0 => 'docs'
                                ]
                            ]
                        ]
                    ]
                ],
                'templates' => [
                    0 => [
                        'name' => $template
                    ],
                    1 => [
                        'location' => 'https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/' . $template
                    ]
                ]
            ]
        ];

        return $phpdoc2Array;
    }
}
