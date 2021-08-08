<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\TableOfContents\Entry;

use function array_filter;

final class TocDescriptor implements Descriptor
{
    /** @var string */
    private $name;

    /** @var Collection<Entry> */
    private $entries;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->entries = Collection::fromClassString(Entry::class);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?DocBlock\DescriptionDescriptor
    {
        return null;
    }

    public function addEntry(TableOfContents\Entry $entry): void
    {
        $this->entries->set($entry->getUrl(), $entry);
    }

    /** @return Collection<Entry> */
    public function getRoots(): Collection
    {
        return Collection::fromClassString(
            Entry::class,
            array_filter(
                $this->entries->getAll(),
                static function (Entry $entry) {
                    return $entry->getParent() === null;
                }
            )
        );
    }
}
