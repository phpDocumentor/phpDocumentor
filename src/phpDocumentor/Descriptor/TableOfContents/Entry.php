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
    /** @var Collection<Entry> */
    private readonly Collection $children;

    public function __construct(
        private readonly string $url,
        private readonly string $title,
        private readonly string|null $parent = null,
    ) {
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

    public function getParent(): string|null
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
