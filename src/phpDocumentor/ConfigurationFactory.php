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
            $array = $this->convertPhpdoc3XmlToArray($this->xml);
        } else {
            $array = $this->convertPhpdoc2XmlToArray($this->xml);
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
     * Converts the phpDocumentor2 configuration xml to an array
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @return array
     */
    private function convertPhpdoc2XmlToArray(\SimpleXMLElement $phpDocumentor)
    {
        $extensions         = [];
        $markers            = [];
        $visibility         = 'public';
        $defaultPackageName = 'Default';
        $template           = 'clean';
        $ignoreHidden       = true;
        $ignoreSymlinks     = true;

        if (isset($phpDocumentor->parser)) {
            $extensions = $this->buildExtensionsPart($phpDocumentor->parser);
            $markers    = $this->buildMarkersPart($phpDocumentor->parser);

            $visibility         = ((string) $phpDocumentor->parser->visibility) ?: $visibility;
            $defaultPackageName = ((string) $phpDocumentor->parser->{'default-package-name'}) ?: $defaultPackageName;
            $template           = ((string) $phpDocumentor->transformations->template->attributes()->name) ?: $template;

            if (isset($phpDocumentor->parser->files)) {
                if (isset($phpDocumentor->parser->files->{'ignore-hidden'})) {
                    $ignoreHidden = filter_var($phpDocumentor->parser->files->{'ignore-hidden'}, FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($phpDocumentor->parser->files->{'ignore-symlinks'})) {
                    $ignoreSymlinks = filter_var($phpDocumentor->parser->files->{'ignore-symlinks'}, FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        $outputDirectory = ((string) $phpDocumentor->parser->target) ?: 'file://build/docs';
        $sourcePath      = ((string) $phpDocumentor->parser->files->directory) ?: 'src';

        $phpdoc2Array = [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => (string) (new Dsn($outputDirectory))->getPath(),
                    'cache'  => '/tmp/phpdoc-doc-cache'
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => '',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => (string) (new Dsn($sourcePath))->getPath(),
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

    /**
     * Converts the phpDocumentor3 configuration xml to an array
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @return array
     */
    private function convertPhpdoc3XmlToArray(\SimpleXMLElement $phpDocumentor)
    {
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
                'format'               => 'php',
                'source'               => [
                    'dsn'   => 'file://.',
                    'paths' => [
                        0 => 'src'
                    ]
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

    /**
     * Builds the extensions part of the array from the phpDocumentor2 configuration xml
     *
     * @param \SimpleXMLElement $parser
     *
     * @return array
     */
    private function buildExtensionsPart(\SimpleXMLElement $parser)
    {
        $extensions = [];
        if (isset($parser->extensions)) {
            foreach ($parser->extensions->children() as $extension) {
                if ((string) $extension !== '') {
                    $extensions[] = (string) $extension;
                }
            }
        }

        return $extensions;
    }

    /**
     * Builds the markers part of the array from the phpDocumentor2 configuration xml
     *
     * @param \SimpleXMLElement $parser
     *
     * @return array
     */
    private function buildMarkersPart(\SimpleXMLElement $parser)
    {
        $markers = [];
        if (isset($parser->markers)) {
            foreach ($parser->markers->children() as $marker) {
                $markers[] = (string) $marker;
            }
        }

        return $markers;
    }
}
