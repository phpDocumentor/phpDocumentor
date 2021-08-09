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

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\References\ResolvedReference;
use Psr\Log\LoggerInterface;

use function array_shift;
use function dirname;
use function implode;
use function in_array;
use function sprintf;
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
    private $url = null;

    /** @var Reference[] */
    private $references = [];

    /** @var Metas */
    private $metas;

    /** @var string[] */
    private $dependencies = [];

    /** @var string[] */
    private $unresolvedDependencies = [];

    /** @var string[] */
    private $originalDependencyNames = [];

    /** @var string[] */
    private $variables = [];

    /** @var string[] */
    private $links = [];

    /** @var int[] */
    private $levels = [];

    /** @var int[] */
    private $counters = [];

    /** @var string[] */
    private $anonymous = [];

    /** @var InvalidLink[] */
    private $invalidLinks = [];

    /** @var LoggerInterface */
    private $logger;

    /** @var Renderer */
    private $renderer;

    /** @var NodeRenderers\NodeRendererFactory */
    private $nodeRendererFactory;

    /** @var string */
    private $outputFolder;

    public function __construct(
        Configuration $configuration,
        Renderer $renderer,
        LoggerInterface $logger,
        FilesystemInterface $origin,
        Metas $metas
    ) {
        $this->outputFolder = $configuration->getOutputFolder();
        $this->initialHeaderLevel = $configuration->getInitialHeaderLevel();
        $this->renderer = $renderer;
        $this->origin = $origin;
        $this->logger = $logger;
        $this->urlGenerator = new UrlGenerator();
        $this->metas = $metas;

        $this->reset();
    }

    public function reset(): void
    {
        $this->titleLetters = [];
        $this->currentTitleLevel = 0;
        $this->levels = [];
        $this->counters = [];

        for ($level = 0; $level < 16; $level++) {
            $this->levels[$level] = 1;
            $this->counters[$level] = 0;
        }
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
        return $this->renderer;
    }

    public function registerReference(Reference $reference): void
    {
        $this->references[$reference->getName()] = $reference;
    }

    public function resolve(string $section, string $data): ?ResolvedReference
    {
        if (!isset($this->references[$section])) {
            $this->addMissingReferenceSectionError($section);

            return null;
        }

        $reference = $this->references[$section];

        $resolvedReference = $reference->resolve($this, $data);

        if ($resolvedReference === null) {
            $this->addInvalidLink(new InvalidLink($data));

            if ($this->getMetaEntry() !== null) {
                $this->getMetaEntry()->removeDependency(
                // use the original name
                    $this->originalDependencyNames[$data] ?? $data
                );
            }

            return null;
        }

        if (isset($this->unresolvedDependencies[$data]) && $this->getMetaEntry() !== null) {
            $this->getMetaEntry()->resolveDependency(
            // use the unique, unresolved name
                $this->unresolvedDependencies[$data],
                $resolvedReference->getFile()
            );
        }

        return $resolvedReference;
    }

    public function addInvalidLink(InvalidLink $invalidLink): void
    {
        $this->invalidLinks[] = $invalidLink;
    }

    /**
     * @return InvalidLink[]
     */
    public function getInvalidLinks(): array
    {
        return $this->invalidLinks;
    }

    /**
     * @return string[]|null
     */
    public function found(string $section, string $data): ?array
    {
        if (isset($this->references[$section])) {
            $reference = $this->references[$section];

            $reference->found($this, $data);

            return null;
        }

        $this->addMissingReferenceSectionError($section);

        return null;
    }

    /**
     * @param mixed $value
     */
    public function setVariable(string $variable, $value): void
    {
        $this->variables[$variable] = $value;
    }

    /**
     * @todo is this used?
     */
    public function createTitle(int $level): string
    {
        for ($currentLevel = 0; $currentLevel < 16; $currentLevel++) {
            if ($currentLevel <= $level) {
                continue;
            }

            $this->levels[$currentLevel] = 1;
            $this->counters[$currentLevel] = 0;
        }

        $this->levels[$level] = 1;
        $this->counters[$level]++;
        $token = ['title'];

        for ($i = 1; $i <= $level; $i++) {
            $token[] = $this->counters[$i];
        }

        return implode('.', $token);
    }

    public function getNumber(int $level): int
    {
        return $this->levels[$level]++;
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getVariable(string $variable, $default = null)
    {
        if (isset($this->variables[$variable])) {
            return $this->variables[$variable];
        }

        return $default;
    }

    public function setLink(string $name, string $url): void
    {
        $name = trim(strtolower($name));

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
        $this->anonymous[] = trim(strtolower($name));
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
        $name = trim(strtolower($name));

        if (isset($this->links[$name])) {
            $link = $this->links[$name];

            if ($relative) {
                return $this->relativeUrl($link);
            }

            return $link;
        }

        return '';
    }

    public function addDependency(string $dependency, bool $requiresResolving = false): void
    {
        if ($requiresResolving) {
            // a hack to avoid collisions between resolved and unresolved dependencies
            $dependencyName = 'UNRESOLVED__' . $dependency;
            $this->unresolvedDependencies[$dependency] = $dependencyName;
            // map the original dependency name to the one that will be stored
            $this->originalDependencyNames[$dependency] = $dependencyName;
        } else {
            // the dependency is already a filename, probably a :doc:
            // or from a toc-tree - change it to the canonical URL
            $canonicalDependency = $this->canonicalUrl($dependency);

            if ($canonicalDependency === null) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Could not get canonical url for dependency %s',
                        $dependency
                    )
                );
            }

            $dependencyName = $canonicalDependency;
            // map the original dependency name to the one that will be stored
            $this->originalDependencyNames[$dependency] = $canonicalDependency;
        }

        if (in_array($dependencyName, $this->dependencies, true)) {
            return;
        }

        $this->dependencies[] = $dependencyName;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
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
            $this->outputFolder,
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

    public function getUrl(): string
    {
        if ($this->url !== null) {
            return $this->url;
        }

        return $this->currentFileName;
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

    private function addMissingReferenceSectionError(string $section): void
    {
        $this->addError(
            sprintf(
                'Unknown reference section "%s"%s',
                $section,
                $this->getCurrentFileName() !== '' ? sprintf(' in "%s" ', $this->getCurrentFileName()) : ''
            )
        );
    }

    public function setNodeRendererFactory(NodeRenderers\NodeRendererFactory $nodeRendererFactory): void
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function getNodeRendererFactory(): NodeRenderers\NodeRendererFactory
    {
        return $this->nodeRendererFactory;
    }
}
