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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\InheritsFromElement;
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;

trait HasTags
{
    /** @var Collection<Collection<TagDescriptor>> $tags The tags associated with this element. */
    protected Collection $tags;

    /**
     * Sets the tags associated with this element.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<Collection<TagDescriptor>> $tags
     */
    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * Returns the tags associated with this element.
     *
     * @return Collection<Collection<TagDescriptor>>
     */
    public function getTags(): Collection
    {
        if (! isset($this->tags)) {
            $this->tags = new Collection();
        }

        return $this->tags;
    }

    /** @return Collection<AuthorDescriptor> */
    public function getAuthor(): Collection
    {
        /** @var Collection<AuthorDescriptor> $author */
        $author = $this->getTags()->fetch('author', new Collection());
        if ($author->count() !== 0) {
            return $author;
        }

        if ($this instanceof InheritsFromElement) {
            $inheritedElement = $this->getInheritedElement();
            if ($inheritedElement instanceof self) {
                return $inheritedElement->getAuthor();
            }
        }

        return new Collection();
    }

    /**
     * Returns the versions for this element.
     *
     * @return Collection<VersionDescriptor>
     */
    public function getVersion(): Collection
    {
        /** @var Collection<VersionDescriptor> $version */
        $version = $this->getTags()->fetch('version', new Collection());
        if ($version->count() !== 0) {
            return $version;
        }

        if ($this instanceof InheritsFromElement) {
            $inheritedElement = $this->getInheritedElement();
            if ($inheritedElement instanceof self) {
                return $inheritedElement->getVersion();
            }
        }

        return new Collection();
    }

    /**
     * Returns the copyrights for this element.
     *
     * @return Collection<TagDescriptor>
     */
    public function getCopyright(): Collection
    {
        /** @var Collection<TagDescriptor> $copyright */
        $copyright = $this->getTags()->fetch('copyright', new Collection());
        if ($copyright->count() !== 0) {
            return $copyright;
        }

        if ($this instanceof InheritsFromElement) {
            $inheritedElement = $this->getInheritedElement();
            if ($inheritedElement instanceof self) {
                return $inheritedElement->getCopyright();
            }
        }

        return new Collection();
    }
}
