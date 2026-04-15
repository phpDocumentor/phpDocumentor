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

use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasName;

use function array_filter;

final class TocDescriptor implements Descriptor
{
    use HasName;
    use HasDescription;

    /** @var Collection<Entry> */
    private readonly Collection $entries;

    private int $count = 0;

    public function __construct(string $name)
    {
        $this->setName($name);
        $this->entries = Collection::fromClassString(Entry::class);
    }

    public function addEntry(TableOfContents\Entry $entry): void
    {
        $this->entries->set($this->count++, $entry);
    }

    /** @return Collection<Entry> */
    public function getRoots(): Collection
    {
        return Collection::fromClassString(
            Entry::class,
            array_filter(
                $this->entries->getAll(),
                static fn (Entry $entry) => $entry->getParent() === null,
            ),
        );
    }
}
