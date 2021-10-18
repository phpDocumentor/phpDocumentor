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
use phpDocumentor\Guides\Nodes\SpanNode;
use Psr\Log\LoggerInterface;
use RuntimeException;

use function array_shift;
use function dirname;
use function strtolower;
use function trim;

class Environment
{
    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var int */
    private $initialHeaderLevel;

    /** @var int */
    private $currentTitleLevel = 0;

    /** @var string[] */
    private $titleLetters = [];

    /** @var string */
    private $currentFileName = '';

    /** @var FilesystemInterface */
    private $origin;

    /** @var string */
    private $currentDirectory = '.';

    /** @var string|null */
    private $url;

    /** @var Metas */
    private $metas;

    /** @var string[] */
    private $variables = [];

    /** @var string[] */
    private $links = [];

    /** @var string[] */
    private $anonymous = [];

    /** @var LoggerInterface */
    private $logger;

    /**
     * @var Renderer|null
     * @todo Refactor this out of the environment for consistency
     */
    private $renderer;

    /**
     * @var NodeRenderers\NodeRendererFactory
     * @todo Refactor this out of the environment for consistency
     */
    private $nodeRendererFactory;

    /** @var string */
    private $destinationPath;

    /** @var string */
    private $currentAbsolutePath = '';

    public function __construct(
        string $outputFolder,
        int $initialHeaderLevel,
        ?Renderer $renderer,
        LoggerInterface $logger,
        FilesystemInterface $origin,
        Metas $metas,
        UrlGenerator $urlGenerator
    ) {
        $this->destinationPath = $outputFolder;
        $this->initialHeaderLevel = $initialHeaderLevel;
        $this->renderer = $renderer;
        $this->origin = $origin;
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
        $this->metas = $metas;

        $this->reset();
    }

    public function reset(): void
    {
        $this->titleLetters = [];
        $this->currentTitleLevel = 0;
    }

    public function getInitialHeaderLevel(): int
    {
        return $this->initialHeaderLevel;
    }

    public function setMetas(Metas $metas): void
    {
        $this->metas = $metas;
    }

    public function getRenderer(): Renderer
    {
        if ($this->renderer === null) {
            throw new RuntimeException(
                'A renderer should have been passed before calling getRenderer, perhaps you are calling this'
                . 'during parsing?'
            );
        }

        return $this->renderer;
    }

    /**
     * @param mixed $value
     */
    public function setVariable(string $variable, $value): void
    {
        $this->variables[$variable] = $value;
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getVariable(string $variable, $default = null)
    {
        return $this->variables[$variable] ?? $default;
    }

    /**
     * @return array<string|SpanNode>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setLink(string $name, string $url): void
    {
        $name = strtolower(trim($name));

        if ($name === '_') {
            $name = array_shift($this->anonymous);
        }

        $this->links[$name] = trim($url);
    }

    public function resetAnonymousStack(): void
    {
        $this->anonymous = [];
    }

    public function pushAnonymous(string $name): void
    {
        $this->anonymous[] = strtolower(trim($name));
    }

    /**
     * @return string[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function getLink(string $name, bool $relative = true): string
    {
        $name = strtolower(trim($name));

        if (isset($this->links[$name])) {
            $link = $this->links[$name];

            if ($relative) {
                return $this->relativeUrl($link);
            }

            return $link;
        }

        return '';
    }

    public function relativeUrl(?string $url): string
    {
        return $this->urlGenerator->relativeUrl($url);
    }

    public function absoluteUrl(string $url): string
    {
        return $this->urlGenerator->absoluteUrl($this->getDirName(), $url);
    }

    public function canonicalUrl(string $url): ?string
    {
        return $this->urlGenerator->canonicalUrl($this->getDirName(), $url);
    }

    public function outputUrl(string $url): ?string
    {
        return $this->urlGenerator->absoluteUrl(
            $this->destinationPath,
            $this->canonicalUrl($url)
        );
    }

    public function generateUrl(string $path): string
    {
        return $this->urlGenerator->generateUrl($path, $this->getDirName());
    }

    public function absoluteRelativePath(string $url): string
    {
        return $this->currentDirectory . '/' . $this->getDirName() . '/' . $this->relativeUrl($url);
    }

    public function getDirName(): string
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

    public function setCurrentDirectory(string $directory): void
    {
        $this->currentDirectory = $directory;
    }

    public function getCurrentDirectory(): string
    {
        return $this->currentDirectory;
    }

    public function getDestinationPath(): string
    {
        return $this->destinationPath;
    }

    public function getUrl(): string
    {
        return $this->url ?? $this->currentFileName;
    }

    public function setUrl(string $url): void
    {
        if ($this->getDirName() !== '') {
            $url = $this->getDirName() . '/' . $url;
        }

        $this->url = $url;
    }

    public function getMetas(): Metas
    {
        return $this->metas;
    }

    public function getMetaEntry(): ?Entry
    {
        return $this->metas->get($this->currentFileName);
    }

    public function getLevel(string $letter): int
    {
        foreach ($this->titleLetters as $level => $titleLetter) {
            if ($letter === $titleLetter) {
                return $level;
            }
        }

        $this->currentTitleLevel++;
        $this->titleLetters[$this->currentTitleLevel] = $letter;

        return $this->currentTitleLevel;
    }

    /**
     * @return string[]
     */
    public function getTitleLetters(): array
    {
        return $this->titleLetters;
    }

    public function addError(string $message): void
    {
        $this->logger->error($message);
    }

    public function setNodeRendererFactory(NodeRenderers\NodeRendererFactory $nodeRendererFactory): void
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function getNodeRendererFactory(): NodeRenderers\NodeRendererFactory
    {
        return $this->nodeRendererFactory;
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
}
