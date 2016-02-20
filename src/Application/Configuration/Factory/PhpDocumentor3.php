<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Configuration\Factory;

use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\DomainModel\Path;

/**
 * phpDocumentor3 strategy for converting the configuration xml to an array.
 */
final class PhpDocumentor3 implements Strategy
{
    /**
     * The path to the xsd that is used for validation of the configuration file.
     *
     * @var string
     */
    private $schemaPath;

    /**
     * Initializes the PhpDocumentor3 strategy.
     *
     * @param string $schemaPath
     */
    public function __construct($schemaPath)
    {
        $this->schemaPath = $schemaPath;
    }

    /**
     * @inheritdoc
     */
    public function convert(\SimpleXMLElement $phpDocumentor)
    {
        $this->validate($phpDocumentor);

        $versions  = [];
        $templates = [];

        foreach ($phpDocumentor->children() as $child) {
            switch ($child->getName()) {
                case 'version':
                    $versions[(string) $child->attributes()->number] = $this->buildVersion($child);
                    break;
                case 'template':
                    $templates[] = $this->buildTemplate($child);
                    break;
                default:
                    break;
            }
        }

        $phpdoc3Array = [
            'phpdocumentor' => [
                'use-cache' => $phpDocumentor->{"use-cache"} ?: true,
                'paths'     => [
                    'output' => new Dsn(((string) $phpDocumentor->paths->output) ?: 'file://build/docs'),
                    'cache'  => new Path(((string) $phpDocumentor->paths->cache) ?: '/tmp/phpdoc-doc-cache'),
                ],
                'versions'  => ($versions) ?: $this->defaultVersions(),
                'templates' => ($templates) ?: [$this->defaultTemplate()],
            ],
        ];

        return $phpdoc3Array;
    }

    /**
     * @inheritdoc
     */
    public function match(\SimpleXMLElement $phpDocumentor)
    {
        return (string) $phpDocumentor->attributes()->version === '3';
    }

    /**
     * Builds the versions part of the array from the configuration xml.
     *
     * @param \SimpleXMLElement $version
     *
     * @return array
     */
    private function buildVersion(\SimpleXMLElement $version)
    {
        $apis   = [];
        $guides = [];
        foreach ($version->children() as $child) {
            switch ($child->getName()) {
                case 'api':
                    $apis[] = $this->buildApi($child);
                    break;
                case 'guide':
                    $guides[] = $this->buildGuide($child);
                    break;
                default:
                    break;
            }
        }

        $version = [
            'folder' => (string) $version->folder,
        ];

        if (count($apis) > 0) {
            $version['api'] = $apis;
        }

        if (count($guides) > 0) {
            $version['guide'] = $guides;
        }

        return $version;
    }

    /**
     * Builds the api part of the array from the configuration xml.
     *
     * @param \SimpleXMLElement $api
     *
     * @return array
     */
    private function buildApi(\SimpleXMLElement $api)
    {
        $extensions = [];
        foreach ($api->extensions->children() as $extension) {
            if ((string) $extension !== '') {
                $extensions[] = (string) $extension;
            }
        }

        $ignoreHidden = filter_var($api->ignore->attributes()->hidden, FILTER_VALIDATE_BOOLEAN);

        return [
            'format'               => ((string) $api->attributes()->format) ?: 'php',
            'source'               => [
                'dsn'   => ((string) $api->source->attributes()->dsn) ?: 'file://.',
                'paths' => ((array) $api->source->path) ?: ['.'],
            ],
            'ignore'               => [
                'hidden' => $ignoreHidden,
                'paths'  => (array) $api->ignore->path,
            ],
            'extensions'           => $extensions,
            'visibility'           => (array) $api->visibility,
            'default-package-name' => ((string) $api->{'default-package-name'}) ?: 'Default',
            'markers'              => (array) $api->markers->children()->marker,
        ];
    }

    /**
     * Builds the guide part of the array from the configuration xml.
     *
     * @param \SimpleXMLElement $guide
     *
     * @return array
     */
    private function buildGuide(\SimpleXMLElement $guide)
    {
        return [
            'format' => ((string) $guide->attributes()->format) ?: 'rst',
            'source' => [
                'dsn'   => ((string) $guide->source->attributes()->dsn) ?: 'file://.',
                'paths' => ((array) $guide->source->path) ?: [''],
            ],
        ];
    }

    /**
     * Builds the template part of the array from the configuration xml.
     *
     * @param \SimpleXMLElement $template
     *
     * @return array
     */
    private function buildTemplate(\SimpleXMLElement $template)
    {
        if ((array) $template === []) {
            return $this->defaultTemplate();
        }

        $attributes = [];
        foreach ($template->attributes() as $attribute) {
            $attributes[$attribute->getName()] = (string) $attribute;
        }

        return $attributes;
    }

    /**
     * Default versions part if none is found in the configuration.
     *
     * @return array
     */
    private function defaultVersions()
    {
        return [
            '1.0.0' => [
                'folder' => 'latest',
                'api'    => [
                    0 => [
                        'format'               => 'php',
                        'source'               => [
                            'dsn'   => 'file://.',
                            'paths' => [
                                0 => 'src'
                            ]
                        ],
                        'ignore'               => [
                            'hidden' => true,
                            'paths'  => []
                        ],
                        'extensions'           => [
                            0 => 'php',
                            1 => 'php3',
                            2 => 'phtml'
                        ],
                        'visibility'           => ['public'],
                        'default-package-name' => 'Default',
                        'markers'              => [
                            0 => 'TODO',
                            1 => 'FIXME'
                        ]
                    ]
                ],
                'guide'  => [
                    0 => [
                        'format' => 'rst',
                        'source' => [
                            'dsn'   => 'file://.',
                            'paths' => [
                                0 => 'docs'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Default template part if none is found in the configuration.
     *
     * @return array
     */
    private function defaultTemplate()
    {
        return [
            'name' => 'clean'
        ];
    }

    /**
     * Validates the configuration xml structure against the schema defined in the schemaPath.
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @throws \InvalidArgumentException if the xml structure is not valid.
     */
    private function validate(\SimpleXMLElement $phpDocumentor)
    {
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        $dom        = new \DOMDocument();
        $domElement = dom_import_simplexml($phpDocumentor);
        $domElement = $dom->importNode($domElement, true);
        $dom->appendChild($domElement);

        $dom->schemaValidate($this->schemaPath);

        $error = libxml_get_last_error();

        if ($error !== false) {
            throw new \InvalidArgumentException($error->message);
        }
    }
}
