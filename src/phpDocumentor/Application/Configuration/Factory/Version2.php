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
 * phpDocumentor2 strategy for converting the configuration xml to an array.
 */
final class Version2 implements Strategy
{
    private $extensions = ['php', 'php3', 'phtml'];

    private $markers = ['TODO', 'FIXME'];

    private $visibility = ['public'];

    private $defaultPackageName = 'Default';

    private $template = 'clean';

    private $ignoreHidden = true;

    private $ignoreSymlinks = true;

    private $ignorePaths = [];

    private $outputDirectory = 'file://build/docs';

    private $directories = ['src'];

    /**
     * @inheritdoc
     */
    public function convert(\SimpleXMLElement $phpDocumentor): array
    {
        $this->validate($phpDocumentor);

        $outputDirectory = $this->buildOutputDirectory($phpDocumentor);

        return [
            'phpdocumentor' => [
                'title' => 'my-doc',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn($outputDirectory),
                    'cache' => new Path('/tmp/phpdoc-doc-cache'),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api' => [
                            0 => [
                                'encoding' => 'utf8',
                                'ignore-tags' => [],
                                'format' => 'php',
                                'validate' => false,
                                'source' => [
                                    'dsn' => new Dsn('file://' . getcwd()),
                                    'paths' => $this->buildSourcePaths($phpDocumentor),
                                ],
                                'ignore' => [
                                    'hidden' => $this->buildIgnoreHidden($phpDocumentor),
                                    'symlinks' => $this->buildIgnoreSymlinks($phpDocumentor),
                                    'paths' => $this->buildIgnorePaths($phpDocumentor),
                                ],
                                'extensions' => $this->buildExtensions($phpDocumentor),
                                'visibility' => $this->buildVisibility($phpDocumentor),
                                'default-package-name' => $this->buildDefaultPackageName($phpDocumentor),
                                'markers' => $this->buildMarkers($phpDocumentor),
                            ],
                        ],
                    ],
                ],
                'templates' => [
                    [
                        'name' => $this->buildTemplate($phpDocumentor),
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function match(\SimpleXMLElement $phpDocumentor): bool
    {
        return !isset($phpDocumentor->attributes()->version);
    }

    /**
     * Loops over a node and fills an array with the found children.
     */
    private function buildArrayFromNode(\SimpleXMLElement $node): array
    {
        $array = [];
        foreach ($node->children() as $child) {
            if ((string) $child !== '') {
                $array[] = (string) $child;
            }
        }

        return $array;
    }

    /**
     * Builds the extensions part of the array from the configuration xml.
     */
    private function buildExtensions(\SimpleXMLElement $phpDocumentor): array
    {
        if ((array) $phpDocumentor->parser === []) {
            return $this->extensions;
        }

        if ((array) $phpDocumentor->parser->extensions === []) {
            return $this->extensions;
        }

        return $this->buildArrayFromNode($phpDocumentor->parser->extensions);
    }

    /**
     * Builds the markers part of the array from the configuration xml.
     */
    private function buildMarkers(\SimpleXMLElement $phpDocumentor): array
    {
        if ((array) $phpDocumentor->parser === []) {
            return $this->markers;
        }

        if ((array) $phpDocumentor->parser->markers === []) {
            return $this->markers;
        }

        return $this->buildArrayFromNode($phpDocumentor->parser->markers);
    }

    /**
     * Builds the visibility part of the array from the configuration xml.
     *
     * @return string[]
     */
    private function buildVisibility(\SimpleXMLElement $phpDocumentor): array
    {
        if ((array) $phpDocumentor->parser === []) {
            return $this->visibility;
        }

        if ((string) $phpDocumentor->parser->visibility === '') {
            return $this->visibility;
        }

        return [(string) $phpDocumentor->parser->visibility];
    }

    /**
     * Builds the defaultPackageName part of the array from the configuration xml.
     *
     * @return string
     */
    private function buildDefaultPackageName(\SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->parser === []) {
            return $this->defaultPackageName;
        }

        if ((string) $phpDocumentor->parser->{'default-package-name'} === '') {
            return $this->defaultPackageName;
        }

        return (string) $phpDocumentor->parser->{'default-package-name'};
    }

    /**
     * Builds the template part of the array from the configuration xml.
     *
     * @return string
     */
    private function buildTemplate(\SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->transformations === []) {
            return $this->template;
        }

        if ((string) $phpDocumentor->transformations->template === '') {
            return $this->template;
        }

        return (string) $phpDocumentor->transformations->template->attributes()->name;
    }

    /**
     * Builds the ignore-hidden part of the array from the configuration xml.
     *
     * @return mixed
     */
    private function buildIgnoreHidden(\SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->files === []) {
            return $this->ignoreHidden;
        }

        if ((string) $phpDocumentor->files->{'ignore-hidden'} === '') {
            return $this->ignoreHidden;
        }

        return filter_var($phpDocumentor->files->{'ignore-hidden'}, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Builds the ignore-symlinks part of the array from the configuration xml.
     *
     * @return mixed
     */
    private function buildIgnoreSymlinks(\SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->files === []) {
            return $this->ignoreSymlinks;
        }

        if ((string) $phpDocumentor->files->{'ignore-symlinks'} === '') {
            return $this->ignoreSymlinks;
        }

        return filter_var($phpDocumentor->files->{'ignore-symlinks'}, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Builds the ignorePaths part of the array from the configuration xml.
     *
     * @return mixed
     */
    private function buildIgnorePaths(\SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->files === []) {
            return $this->ignorePaths;
        }
        $ignorePaths = [];
        foreach ($phpDocumentor->files->children() as $child) {
            if ($child->getName() === 'ignore') {
                $ignorePaths[] = (string) $child;
            }
        }

        if (count($ignorePaths) === 0) {
            return $this->ignorePaths;
        }

        return $ignorePaths;
    }

    /**
     * Builds the outputDirectory part of the array from the configuration xml.
     *
     * @return string
     */
    private function buildOutputDirectory(\SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->transformer === []) {
            return $this->outputDirectory;
        }

        if ((string) $phpDocumentor->transformer->target === '') {
            return $this->outputDirectory;
        }

        return (string) $phpDocumentor->transformer->target;
    }

    /**
     * Builds the directories that are used in the sourcePaths.
     *
     * @return array
     */
    private function buildDirectories(\SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->files === []) {
            return $this->directories;
        }

        if ((string) $phpDocumentor->files->directory === '') {
            return $this->directories;
        }

        return (array) $phpDocumentor->files->directory;
    }

    /**
     * Builds the sourcePaths part of the array from the configuration xml.
     *
     * @return array
     */
    private function buildSourcePaths(\SimpleXMLElement $phpDocumentor)
    {
        $sourcePaths = [];
        $directories = $this->buildDirectories($phpDocumentor);

        foreach ($directories as $directory) {
            $sourcePaths[] = (new Dsn($directory))->getPath();
        }

        return $sourcePaths;
    }

    /**
     * Validates if the xml has a root element which name is phpdocumentor.
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
