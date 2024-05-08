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
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasName;

final class VersionDescriptor implements CompilableSubject, Descriptor
{
    use HasName;
    use HasDescription;

    /** @param Collection<DocumentationSetDescriptor> $documentationSets */
    public function __construct(private readonly string $number, private readonly Collection $documentationSets)
    {
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    /** @return Collection<DocumentationSetDescriptor> */
    public function getDocumentationSets(): Collection
    {
        return $this->documentationSets;
    }

    /** @return Collection<TocDescriptor> */
    public function getTableOfContents(): Collection
    {
        $tocs = Collection::fromClassString(TocDescriptor::class);
        foreach ($this->documentationSets as $documentationSet) {
            $tocs = $documentationSet->getTableOfContents()->merge($tocs);
        }

        return $tocs;
    }
}
