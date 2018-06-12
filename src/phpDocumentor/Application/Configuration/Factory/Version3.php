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
final class Version3 implements Strategy
{
    /**
     * The path to the xsd that is used for validation of the configuration file.
     *
     * @var string
     */
    private $schemaPath;

    /**
     * Initializes the PhpDocumentor3 strategy.
     */
    public function __construct(string $schemaPath)
    {
        $this->schemaPath = $schemaPath;
    }

    /**
     * @inheritdoc
     */
    public function convert(\SimpleXMLElement $phpDocumentor): array
    {
        $this->validate($phpDocumentor);

        $versions = [];
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

        return [
            'phpdocumentor' => [
                'use-cache' => $phpDocumentor->{'use-cache'} ?: true,
                'paths' => [
                    'output' => new Dsn(((string) $phpDocumentor->paths->output) ?: 'file://build/docs'),
                    'cache' => new Path(((string) $phpDocumentor->paths->cache) ?: '/tmp/phpdoc-doc-cache'),
                ],
                'versions' => ($versions) ?: $this->defaultVersions(),
                'templates' => ($templates) ?: [$this->defaultTemplate()],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function match(\SimpleXMLElement $phpDocumentor): bool
    {
        return (string) $phpDocumentor->attributes()->version === '3';
    }

    public static function buildDefault()
    {
        return [
            'phpdocumentor' => [
                'title' => 'my docs',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn('file://build/docs'),
                    'cache' => new Path('/tmp/phpdoc-doc-cache'),
                ],
                'versions' => static::defaultVersions(),
                'templates' => [static::defaultTemplate()],
            ],
        ];
    }

    /**
     * Builds the versions part of the array from the configuration xml.
     */
    private function buildVersion(\SimpleXMLElement $version): array
    {
        $apis = [];
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
     */
    private function buildApi(\SimpleXMLElement $api): array
    {
        $extensions = [];
        foreach ($api->extensions->children() as $extension) {
            if ((string) $extension !== '') {
                $extensions[] = (string) $extension;
            }
        }

        $ignoreHidden = filter_var($api->ignore->attributes()->hidden, FILTER_VALIDATE_BOOLEAN);

        return [
            'format' => ((string) $api->attributes()->format) ?: 'php',
            'source' => [
                'dsn' => ((string) $api->source->attributes()->dsn) ?: 'file://.',
                'paths' => ((array) $api->source->path) ?: ['.'],
            ],
            'ignore' => [
                'hidden' => $ignoreHidden,
                'paths' => (array) $api->ignore->path,
            ],
            'extensions' => $extensions,
            'visibility' => (array) $api->visibility,
            'default-package-name' => ((string) $api->{'default-package-name'}) ?: 'Default',
            'markers' => (array) $api->markers->children()->marker,
        ];
    }

    /**
     * Builds the guide part of the array from the configuration xml.
     */
    private function buildGuide(\SimpleXMLElement $guide): array
    {
        return [
            'format' => ((string) $guide->attributes()->format) ?: 'rst',
            'source' => [
                'dsn' => ((string) $guide->source->attributes()->dsn) ?: 'file://.',
                'paths' => ((array) $guide->source->path) ?: [''],
            ],
        ];
    }

    /**
     * Builds the template part of the array from the configuration xml.
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
     */
    private static function defaultVersions(): array
    {
        return [
            '1.0.0' => [
                'folder' => 'latest',
                'api' => [
                    0 => [
                        'format' => 'php',
                        'source' => [
                            'dsn' => new Dsn('file://.'),
                            'paths' => [
                                0 => new Path('src'),
                            ],
                        ],
                        'ignore' => [
                            'hidden' => true,
                            'paths' => [],
                        ],
                        'extensions' => [
                            0 => 'php',
                            1 => 'php3',
                            2 => 'phtml',
                        ],
                        'visibility' => ['public'],
                        'default-package-name' => 'Default',
                        'encoding' => 'utf8',
                        'ignore-tags' => [],
                        'validate' => false,
                        'markers' => [
                            0 => 'TODO',
                            1 => 'FIXME',
                        ],
                    ],
                ],
                'guide' => [
                    0 => [
                        'format' => 'rst',
                        'source' => [
                            'dsn' => new Dsn('file://.'),
                            'paths' => [
                                0 => new Path('docs'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Default template part if none is found in the configuration.
     */
    private static function defaultTemplate(): array
    {
        return [
            'name' => 'clean',
        ];
    }

    /**
     * Validates the configuration xml structure against the schema defined in the schemaPath.
     *
     * @throws \InvalidArgumentException if the xml structure is not valid.
     */
    private function validate(\SimpleXMLElement $phpDocumentor)
    {
        libxml_clear_errors();
        $priorSetting = libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $domElement = dom_import_simplexml($phpDocumentor);
        $domElement = $dom->importNode($domElement, true);
        $dom->appendChild($domElement);

        $dom->schemaValidate($this->schemaPath);

        $error = libxml_get_last_error();

        if ($error !== false) {
            throw new \InvalidArgumentException(trim($error->message));
        }

        libxml_use_internal_errors($priorSetting);
    }
}
