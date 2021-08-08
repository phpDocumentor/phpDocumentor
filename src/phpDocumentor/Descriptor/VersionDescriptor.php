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

final class VersionDescriptor
{
    /** @var string */
    private $number;

    /** @var Collection<DocumentationSetDescriptor> */
    private $documentationSets;

    /**
     * @param Collection<DocumentationSetDescriptor> $documentationSets
     */
    public function __construct(string $number, Collection $documentationSets)
    {
        $this->documentationSets = $documentationSets;
        $this->number = $number;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return Collection<DocumentationSetDescriptor>
     */
    public function getDocumentationSets(): Collection
    {
        return $this->documentationSets;
    }

    /** @return Collection<TocDescriptor> */
    public function getTableOfContents(): Collection
    {
        $tocs = Collection::fromClassString(TocDescriptor::class);
        foreach ($this->documentationSets as $documentationSet) {
            $tocs = $tocs->merge($documentationSet->getTableOfContents());
        }

        return $tocs;
    }
}
