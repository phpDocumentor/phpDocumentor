<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\Nodes\DocumentNode;

use function array_shift;
use function dirname;
use function strtolower;
use function trim;

class RenderContext
{
    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var string */
    private $currentFileName = '';

    /** @var FilesystemInterface */
    private $origin;

    /** @var Metas */
    private $metas;

    /** @var string[] */
    private $links = [];

    /** @var string */
    private $destinationPath;

    /** @var string */
    private $currentAbsolutePath = '';

    private string $outputFormat;
    private DocumentNode $document;
    private FilesystemInterface $destination;

    public function __construct(
        string $outputFolder,
        FilesystemInterface $origin,
        FilesystemInterface $destination,
        Metas $metas,
        UrlGenerator $urlGenerator,
        string $outputFormat
    ) {
        $this->destinationPath = $outputFolder;
        $this->origin = $origin;
        $this->urlGenerator = $urlGenerator;
        $this->metas = $metas;
        $this->outputFormat = $outputFormat;
        $this->destination = $destination;
    }

    public function setDocument(DocumentNode $documentNode): void
    {
        $this->document = $documentNode;
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getVariable(string $variable, $default = null)
    {
        return $this->document->getVariable($variable, $default);
    }

    public function setLink(string $name, string $url): void
    {
        $name = strtolower(trim($name));
        $this->links[$name] = trim($url);
    }

    public function getLink(string $name, bool $relative = true): string
    {
        $name = strtolower(trim($name));

        if (isset($this->links[$name])) {
            $link = $this->links[$name];

            if ($relative) {
                return $this->urlGenerator->relativeUrl($link);
            }

            return $link;
        }

        return '';
    }

    public function canonicalUrl(string $url): ?string
    {
        return $this->urlGenerator->canonicalUrl($this->getDirName(), $url);
    }

    public function relativeDocUrl(string $filename, ?string $anchor = null): string
    {
        return $this->urlGenerator->relativeUrl(
            $this->destinationPath . '/' .
            $filename . '.' . $this->outputFormat .
            ($anchor !== null ? '#' . $anchor : '')
        );
    }

    private function getDirName(): string
    {
        $dirname = dirname($this->currentFileName);

        if ($dirname === '.') {
            return '';
        }

        return $dirname;
    }

    public function setCurrentFileName(string $filename): void
    {
        $this->currentFileName = $filename;
    }

    public function getCurrentFileName(): string
    {
        return $this->currentFileName;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getMetas(): Metas
    {
        return $this->metas;
    }

    public function getMetaEntry(): ?Entry
    {
        return $this->metas->get($this->currentFileName);
    }

    /**
     * Register the current file's absolute path on the Origin file system.
     *
     * You would more or less expect getCurrentFileName to return this information; but that filename does
     * not return the absolute position on Origin but the relative path from the Documentation Root.
     */
    public function setCurrentAbsolutePath(string $path): void
    {
        $this->currentAbsolutePath = $path;
    }

    /**
     * Return the current file's absolute path on the Origin file system.
     *
     * In order to load files relative to the current file (such as embedding UML diagrams) the environment
     * must expose what the absolute path relative to the Origin is.
     *
     * @see self::setCurrentAbsolutePath() for more information
     * @see self::getOrigin() for the filesystem on which to use this path
     */
    public function getCurrentAbsolutePath(): string
    {
        return $this->currentAbsolutePath;
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    public function getDestinationPath(): string
    {
        return $this->destinationPath;
    }

    public function setDestinationPath(string $path): void
    {
        $this->destinationPath = $path;
    }

    public function getDestination(): FilesystemInterface
    {
        return $this->destination;
    }
}
