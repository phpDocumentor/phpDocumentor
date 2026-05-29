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

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\TocDescriptor;

interface VersionInterface extends Descriptor
{
    /**
     * Returns the name for this element.
     */

    public function getNumber(): string;

    /** @return Collection<DocumentationSetDescriptor> */
    public function getDocumentationSets(): Collection;

    /** @return Collection<TocDescriptor> */
    public function getTableOfContents(): Collection;
}
