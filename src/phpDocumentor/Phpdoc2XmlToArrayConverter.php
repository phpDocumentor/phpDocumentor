<?php

namespace phpDocumentor;

final class Phpdoc2XmlToArrayConverter implements XmlToArrayConverter
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
     * Converts the phpDocumentor2 configuration xml to an array.
     *
     * @return array
     */
    public function convert()
    {
        $phpDocumentor = $this->xml;

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
     * Builds the extensions part of the array from the phpDocumentor2 configuration xml.
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
     * Builds the markers part of the array from the phpDocumentor2 configuration xml.
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
                if ((string) $marker !== '') {
                    $markers[] = (string) $marker;
                }
            }
        }

        return $markers;
    }
}
