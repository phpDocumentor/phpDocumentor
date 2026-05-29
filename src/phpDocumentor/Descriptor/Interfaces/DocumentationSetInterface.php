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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Configuration\Source;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\TocDescriptor;

interface DocumentationSetInterface extends Descriptor
{
    /** @return Collection<TocDescriptor> */
    public function getTableOfContents(): Collection;

    /**
     * Returns the source location for this set of documentation.
     *
     * @todo: should the source location be included in a Descriptor? This couples it to the file system upon which
     *   it was ran and makes it uncacheable. But should this be cached? In any case, I need it for the RenderGuide
     *   writer at the moment; so refactor this once that becomes clearer.
     */
    public function getSource(): Source;

    public function getOutputLocation(): string;

    /**
     * Returns the list of files that is in this documentation set.
     *
     * @return Collection<FileInterface>
     */
    public function getFiles(): Collection;

    /**
     * Returns all indexes in this documentation set.
     *
     * @see setIndexes() for more information on what indexes are.
     *
     * @return CollectionInterface<CollectionInterface<ElementInterface>>
     */
    public function getIndexes(): CollectionInterface;

    /**
     * Returns an index with the given name.
     *
     * If the index does not exist yet, it will dynamically be added to the list of indexes.
     *
     * @return Collection<ElementInterface>
     */
    public function getIndex(string $name): Collection;

    /**
     * Returns the description for this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     */
    public function getDescription(): DescriptionDescriptor;

    /**
     * Returns the name for this element.
     */
    public function getName(): string;
}
