<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration\Factory;

use InvalidArgumentException;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use SimpleXMLElement;

/**
 * phpDocumentor2 strategy for converting the configuration xml to an array.
 */
final class Version2 implements Strategy
{
    /** @var string[] */
    private $extensions = ['php', 'php3', 'phtml'];

    /** @var string[] */
    private $markers = ['TODO', 'FIXME'];

    /** @var string[] */
    private $visibility = ['public', 'protected', 'private'];

    /** @var string */
    private $defaultPackageName = 'Default';

    /** @var string */
    private $template = 'clean';

    /** @var bool */
    private $ignoreHidden = true;

    /** @var bool */
    private $ignoreSymlinks = true;

    /** @var string[] */
    private $ignorePaths = [];

    /** @var string */
    private $outputDirectory = 'file://build/docs';

    /** @var string[] */
    private $directories = ['src'];

    private $includeSource = false;

    public function convert(SimpleXMLElement $phpDocumentor): array
    {
        $this->validate($phpDocumentor);

        $outputDirectory = $this->buildOutputDirectory($phpDocumentor);
        $cacheDirectory = $this->buildCacheDirectory($phpDocumentor);

        return [
            'phpdocumentor' => [
                'title' => ((string)$phpDocumentor->title) ?: 'my-doc',
                'use-cache' => true,
                'paths' => [
                    'output' => new Dsn($outputDirectory),
                    'cache' => new Path($cacheDirectory),
                ],
                'versions' => [
                    '1.0.0' => [
                        'folder' => '',
                        'api' => [
                            0 => [
                                'encoding' => $this->buildEncoding($phpDocumentor),
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
                                'include-source' => $this->buildIncludeSourcecode($phpDocumentor),
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

    public function supports(SimpleXMLElement $phpDocumentor): bool
    {
        return isset($phpDocumentor->attributes()->version) === false
            || $phpDocumentor->attributes()->version == '2';
    }

    /**
     * Loops over a node and fills an array with the found children.
     */
    private function buildArrayFromNode(SimpleXMLElement $node): array
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
     *
     * @return string[]
     */
    private function buildExtensions(SimpleXMLElement $phpDocumentor): array
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
     *
     * @return string[]
     */
    private function buildMarkers(SimpleXMLElement $phpDocumentor): array
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
     * Builds whether the source code should be part of the output.
     */
    private function buildIncludeSourcecode(SimpleXMLElement $phpDocumentor): bool
    {
        if ((array) $phpDocumentor->parser === []) {
            return $this->includeSource;
        }

        return (bool) $phpDocumentor->parser->{'include-source'};
    }

    /**
     * Builds the visibility part of the array from the configuration xml.
     *
     * @return string[]
     */
    private function buildVisibility(SimpleXMLElement $phpDocumentor): array
    {
        if ((array) $phpDocumentor->parser === []) {
            return $this->visibility;
        }

        if ((string) $phpDocumentor->parser->visibility === '') {
            return $this->visibility;
        }

        return explode(',', (string) $phpDocumentor->parser->visibility);
    }

    /**
     * Builds the defaultPackageName part of the array from the configuration xml.
     */
    private function buildDefaultPackageName(SimpleXMLElement $phpDocumentor): string
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
     */
    private function buildTemplate(SimpleXMLElement $phpDocumentor): string
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
     */
    private function buildIgnoreHidden(SimpleXMLElement $phpDocumentor): bool
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
     */
    private function buildIgnoreSymlinks(SimpleXMLElement $phpDocumentor): bool
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
     * @return string[]
     */
    private function buildIgnorePaths(SimpleXMLElement $phpDocumentor): array
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
     */
    private function buildOutputDirectory(SimpleXMLElement $phpDocumentor): string
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
     * @return string[]
     */
    private function buildDirectories(SimpleXMLElement $phpDocumentor): array
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
     * @return Path[]
     */
    private function buildSourcePaths(SimpleXMLElement $phpDocumentor): array
    {
        $sourcePaths = [];
        $directories = $this->buildDirectories($phpDocumentor);

        foreach ($directories as $directory) {
            $sourcePaths[] = (new Dsn($directory))->getPath();
        }

        return $sourcePaths;
    }

    /**
     * Builds the outputDirectory part of the array from the configuration xml.
     */
    private function buildCacheDirectory(SimpleXMLElement $phpDocumentor): string
    {
        if ((array) $phpDocumentor->parser === []) {
            return '/tmp/phpdoc-doc-cache';
        }

        if ((string) $phpDocumentor->parser->target === '') {
            return '/tmp/phpdoc-doc-cache';
        }

        return (string) $phpDocumentor->parser->target;
    }

    /**
     * Validates if the xml has a root element which name is phpdocumentor.
     *
     * @throws InvalidArgumentException if the root element of the xml is not phpdocumentor.
     */
    private function validate(SimpleXMLElement $xml): void
    {
        if ($xml->getName() !== 'phpdocumentor') {
            throw new InvalidArgumentException(
                sprintf('Root element name should be phpdocumentor, %s found', $xml->getName())
            );
        }
    }

    private function buildEncoding(SimpleXMLElement $phpDocumentor)
    {
        if ((array) $phpDocumentor->parser === []) {
            return 'utf-8';
        }

        if ((string) $phpDocumentor->parser->encoding === '') {
            return 'utf-8';
        }

        return (string) $phpDocumentor->parser->encoding;
    }
}
