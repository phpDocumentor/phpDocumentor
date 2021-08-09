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

use phpDocumentor\Configuration\Source;

abstract class DocumentationSetDescriptor
{
    /** @var string */
    protected $name = '';

    /** @var Source */
    protected $source;

    /** @var string */
    protected $output = '.';

    /** @var Collection<TocDescriptor> */
    private $tocs;

    public function __construct()
    {
        $this->tocs = Collection::fromClassString(TocDescriptor::class);
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getOutput(): string
    {
        return $this->output;
    }
}
