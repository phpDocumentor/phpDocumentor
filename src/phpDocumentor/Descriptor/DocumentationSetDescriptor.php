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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Configuration\Source;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasName;

abstract class DocumentationSetDescriptor implements Descriptor, CompilableSubject
{
    use HasName;
    use HasDescription;

    protected Source $source;
    protected string $outputLocation = '.';

    /** @var Collection<TocDescriptor> */
    private readonly Collection $tocs;

    /** @var Collection<FileInterface> $files */
    private Collection $files;

    /** @var Collection<Collection<ElementInterface>> $indexes */
    private Collection $indexes;

    public function __construct()
    {
        $this->tocs = Collection::fromClassString(TocDescriptor::class);
        $this->files = Collection::fromInterfaceString(FileInterface::class);

        /** @phpstan-ignore-next-line */
        $this->indexes = Collection::fromClassString(Collection::class);
    }

    public function addTableOfContents(TocDescriptor $descriptor): void
    {
        $this->tocs->set($descriptor->getName(), $descriptor);
    }

    /** @return Collection<TocDescriptor> */
    public function getTableOfContents(): Collection
    {
        return $this->tocs;
    }

    /**
     * Returns the source location for this set of documentation.
     *
     * @todo: should the source location be included in a Descriptor? This couples it to the file system upon which
     *   it was ran and makes it uncacheable. But should this be cached? In any case, I need it for the RenderGuide
     *   writer at the moment; so refactor this once that becomes clearer.
     */
    public function getSource(): Source
    {
        return $this->source;
    }

    public function getOutputLocation(): string
    {
        return $this->outputLocation;
    }

    /**
     * Sets the list of files that is in this documentation set.
     *
     * @param Collection<FileInterface> $files
     */
    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    /**
     * Returns the list of files that is in this documentation set.
     *
     * @return Collection<FileInterface>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * Sets all indexes for this documentation set.
     *
     * An index is a compilation of references to elements, usually constructed in a compiler step, that aids template
     * generation by providing a conveniently assembled list. An example of such an index is the 'marker' index where
     * a list of TODOs and FIXMEs are located in a central location for reporting.
     *
     * @param Collection<Collection<ElementInterface>> $indexes
     */
    public function setIndexes(Collection $indexes): void
    {
        $this->indexes = $indexes;
    }

    /**
     * Returns all indexes in this documentation set.
     *
     * @see setIndexes() for more information on what indexes are.
     *
     * @return Collection<Collection<ElementInterface>>
     */
    public function getIndexes(): Collection
    {
        return $this->indexes;
    }

    /**
     * Returns an index with the given name.
     *
     * If the index does not exist yet, it will dynamically be added to the list of indexes.
     *
     * @return Collection<ElementInterface>
     */
    public function getIndex(string $name): Collection
    {
        $index = $this->indexes[$name];
        if ($index === null) {
            $index = Collection::fromInterfaceString(ElementInterface::class);
            $this->indexes[$name] = $index;
        }

        return $index;
    }
}
