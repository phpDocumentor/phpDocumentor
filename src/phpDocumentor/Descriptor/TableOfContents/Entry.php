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

namespace phpDocumentor\Descriptor\TableOfContents;

use phpDocumentor\Descriptor\Collection;

final class Entry
{
    /** @var string */
    private $url;

    /** @var string */
    private $title;

    /** @var string|null */
    private $parent;

    /** @var Collection<Entry> */
    private $children;

    public function __construct(string $url, string $title, ?string $parent = null)
    {
        $this->url = $url;
        $this->title = $title;
        $this->parent = $parent;
        $this->children = Collection::fromClassString(self::class);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function addChild(Entry $child): void
    {
        $this->children->set($child->getUrl(), $child);
    }

    /** @return Collection<Entry> */
    public function getChildren(): Collection
    {
        return $this->children;
    }
}
