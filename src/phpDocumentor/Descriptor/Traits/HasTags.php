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
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\Interfaces\InheritsFromElement;
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;

trait HasTags
{
    /** @var CollectionInterface<CollectionInterface<TagDescriptor>> $tags The tags associated with this element. */
    protected CollectionInterface $tags;

    /**
     * Sets the tags associated with this element.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param CollectionInterface<CollectionInterface<TagDescriptor>> $tags
     */
    public function setTags(CollectionInterface $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * Returns the tags associated with this element.
     *
     * @return CollectionInterface<CollectionInterface<TagDescriptor>>
     */
    public function getTags(): CollectionInterface
    {
        if (! isset($this->tags)) {
            $this->tags = new Collection();
        }

        return $this->tags;
    }

    /** @return CollectionInterface<AuthorDescriptor> */
    public function getAuthor(): CollectionInterface
    {
        /** @var CollectionInterface<AuthorDescriptor> $author */
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
     * @return CollectionInterface<VersionDescriptor>
     */
    public function getVersion(): CollectionInterface
    {
        /** @var CollectionInterface<VersionDescriptor> $version */
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
     * @return CollectionInterface<TagDescriptor>
     */
    public function getCopyright(): CollectionInterface
    {
        /** @var CollectionInterface<TagDescriptor> $copyright */
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
