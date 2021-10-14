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

namespace phpDocumentor\Guides\Meta;

use LogicException;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function array_merge;
use function array_search;
use function in_array;
use function is_array;
use function is_string;
use function sprintf;

final class Entry
{
    /** @var string */
    private $file;

    /** @var string */
    private $url;

    /** @var string */
    private $title;

    /** @var string[][]|string[][][] */
    private $titles;

    /** @var mixed[][] */
    private $tocs;

    /** @var int */
    private $mtime;

    /** @var string[] */
    private $depends;

    /** @var string[] */
    private $resolvedDependencies = [];

    /** @var string[] */
    private $links;

    /** @var string|null */
    private $parent;

    /**
     * @param string[][]|string[][][] $titles
     * @param mixed[][] $tocs
     * @param string[] $depends
     * @param string[] $links
     */
    public function __construct(
        string $file,
        string $url,
        string $title,
        array $titles,
        array $tocs,
        array $depends,
        array $links,
        int $mtime
    ) {
        $this->file = $file;
        $this->url = $url;
        $this->title = $title;
        $this->titles = $titles;
        $this->tocs = $tocs;
        $this->depends = $depends;
        $this->links = $links;
        $this->mtime = $mtime;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string[][]|string[][][]
     */
    public function getTitles(): array
    {
        return $this->titles;
    }

    public function hasTitle(string $text): bool
    {
        $titles = $this->getAllTitles();

        $slugger = new AsciiSlugger();
        $text = $slugger->slug($text)->lower()->toString();

        foreach ($titles as $title) {
            if ($text === $slugger->slug($title)->lower()->toString()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed[][]
     */
    public function getTocs(): array
    {
        return $this->tocs;
    }

    /**
     * @return string[]
     */
    public function getDepends(): array
    {
        return $this->depends;
    }

    /**
     * Call to replace a dependency with the resolved, real filename.
     */
    public function resolveDependency(string $originalDependency, ?string $newDependency): void
    {
        if ($newDependency === null) {
            return;
        }

        // we only need to resolve a dependency one time
        if (in_array($originalDependency, $this->resolvedDependencies, true)) {
            return;
        }

        $key = array_search($originalDependency, $this->depends, true);

        if ($key === false) {
            throw new LogicException(
                sprintf('Could not find dependency "%s" in MetaEntry for "%s"', $originalDependency, $this->file)
            );
        }

        $this->depends[$key] = $newDependency;
        $this->resolvedDependencies[] = $originalDependency;
    }

    public function removeDependency(string $dependency): void
    {
        $key = array_search($dependency, $this->depends, true);

        if ($key === false) {
            return;
        }

        unset($this->depends[$key]);
    }

    /**
     * @return string[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function getMtime(): int
    {
        return $this->mtime;
    }

    public function setParent(string $parent): void
    {
        $this->parent = $parent;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @param string[]|string[][]|null $entryTitles
     *
     * @return string[]
     */
    private function getAllTitles(?array $entryTitles = null): array
    {
        if ($entryTitles === null) {
            $entryTitles = $this->titles;
        }

        $titles = [];

        foreach ($entryTitles as $title) {
            if (is_string($title[0])) {
                $titles[] = $title[0];
            }

            if (!is_array($title[1])) {
                continue;
            }

            $titles = array_merge($titles, $this->getAllTitles($title[1]));
        }

        return $titles;
    }
}
