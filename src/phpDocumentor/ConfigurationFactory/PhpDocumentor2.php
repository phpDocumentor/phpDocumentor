<?php

namespace phpDocumentor\ConfigurationFactory;

use phpDocumentor\Dsn;

/**
 * phpDocumentor2 strategy for converting the configuration xml to an array.
 */
final class PhpDocumentor2 implements Strategy
{
    /**
     * Converts the phpDocumentor2 configuration xml to an array.
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @return array
     */
    public function convert(\SimpleXMLElement $phpDocumentor)
    {
        $this->validate($phpDocumentor);

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
                    $ignoreHidden = filter_var(
                        $phpDocumentor->parser->files->{'ignore-hidden'},
                        FILTER_VALIDATE_BOOLEAN
                    );
                }

                if (isset($phpDocumentor->parser->files->{'ignore-symlinks'})) {
                    $ignoreSymlinks = filter_var(
                        $phpDocumentor->parser->files->{'ignore-symlinks'},
                        FILTER_VALIDATE_BOOLEAN
                    );
                }
            }
        }

        $outputDirectory = ((string) $phpDocumentor->parser->target) ?: 'file://build/docs';
        $directories     = ((array) $phpDocumentor->parser->files->directory) ?: ['src'];

        $sourcePaths = [];
        foreach ($directories as $directory) {
            $sourcePaths[] = (string) (new Dsn($directory))->getPath();
        }

        $phpdoc2Array = [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => (string) (new Dsn($outputDirectory))->getPath(),
                    'cache'  => '/tmp/phpdoc-doc-cache',
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => '',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => $sourcePaths,
                            ],
                            'ignore'               => [
                                'hidden'   => $ignoreHidden,
                                'symlinks' => $ignoreSymlinks,
                                'paths'    => ['src/ServiceDefinitions.php'],
                            ],
                            'extensions'           => $extensions,
                            'visibility'           => $visibility,
                            'default-package-name' => $defaultPackageName,
                            'markers'              => $markers,
                        ],
                    ],
                ],
                'templates' => [
                    [
                        'name' => $template,
                    ],
                ],
            ],
        ];

        return $phpdoc2Array;
    }

    /**
     * Checks if the provided configuration xml is for phpDocumentor2.
     *
     * @param $phpDocumentor
     *
     * @return bool
     */
    public function match(\SimpleXMLElement $phpDocumentor)
    {
        return (bool) $phpDocumentor->attributes()->version === false;
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
            throw new \InvalidArgumentException(
                sprintf('Root element name should be phpdocumentor, %s found', $xml->getName())
            );
        }
    }
}
