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

namespace phpDocumentor\ConfigurationFactory;

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
        if ($schemaPath === '') {
            $schemaPath = __DIR__ . '../../../../data/xsd/phpdoc.xsd';
        }

        $this->schemaPath = $schemaPath;
    }

    /**
     * @inheritdoc
     */
    public function convert(\SimpleXMLElement $phpDocumentor)
    {
        $this->validate($phpDocumentor);

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

    /**
     * @inheritdoc
     */
    public function match(\SimpleXMLElement $phpDocumentor)
    {
        return (int) $phpDocumentor->attributes()->version === 3;
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
        if ((array) $template === []) {
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
     * Validates the phpDocumentor3 configuration xml structure against the schema defined in the schemaPath.
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
