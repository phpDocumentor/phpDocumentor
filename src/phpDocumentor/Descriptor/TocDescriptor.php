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

use phpDocumentor\Descriptor\Interfaces\Collection;
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasName;

use function array_filter;

final class TocDescriptor implements Descriptor
{
    use HasName;
    use HasDescription;

    /** @var Collection<Interfaces\TableOfContents\Entry> */
    private readonly Collection $entries;

    private int $count = 0;

    public function __construct(string $name)
    {
        $this->setName($name);
        $this->entries = \phpDocumentor\Descriptor\Collection::fromInterfaceString(
            Interfaces\TableOfContents\Entry::class,
        );
    }

    public function addEntry(Interfaces\TableOfContents\Entry $entry): void
    {
        $this->entries->set($this->count++, $entry);
    }

    /** @return Collection<Interfaces\TableOfContents\Entry> */
    public function getRoots(): Collection
    {
        return \phpDocumentor\Descriptor\Collection::fromInterfaceString(
            Interfaces\TableOfContents\Entry::class,
            array_filter(
                $this->entries->getAll(),
                static fn (Interfaces\TableOfContents\Entry $entry) => $entry->getParent() === null,
            ),
        );
    }
}
