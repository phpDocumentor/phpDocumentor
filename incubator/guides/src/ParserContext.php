<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Nodes\SpanNode;

use function array_shift;
use function dirname;
use function strtolower;
use function trim;

class ParserContext
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
    private $currentFileName;

    /** @var FilesystemInterface */
    private $origin;

    /** @var string */
    private $currentDirectory;

    /** @var string[] */
    private $variables = [];

    /** @var string[] */
    private $links = [];

    /** @var string[] */
    private $anonymous = [];

    /** @var string[] */
    private $errors = [];

    /** @var string */
    private $destinationPath;

    /** @var string */
    private $currentAbsolutePath = '';

    public function __construct(
        string $currentFileName,
        string $currentDirectory,
        string $outputFolder,
        int $initialHeaderLevel,
        FilesystemInterface $origin,
        UrlGenerator $urlGenerator
    ) {
        $this->destinationPath = $outputFolder;
        $this->initialHeaderLevel = $initialHeaderLevel;
        $this->origin = $origin;
        $this->urlGenerator = $urlGenerator;
        $this->currentFileName = $currentFileName;
        $this->currentDirectory = $currentDirectory;

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

    /**
     * @param mixed $value
     */
    public function setVariable(string $variable, $value): void
    {
        $this->variables[$variable] = $value;
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

    private function relativeUrl(?string $url): string
    {
        return $this->urlGenerator->relativeUrl($url);
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

    public function getCurrentFileName(): string
    {
        return $this->currentFileName;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
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
        return $this->currentFileName;
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

    public function addError(string $message): void
    {
        $this->errors[] = $message;
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

    /** @return string[] */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
