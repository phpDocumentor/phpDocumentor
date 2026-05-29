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
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\TocDescriptor;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;

interface GuideDocumentationSet
{
    /** @return Collection<TocDescriptor> */
    public function getTableOfContents(): Collection;

    /**
     * Returns all indexes in this documentation set.
     *
     * @see setIndexes() for more information on what indexes are.
     *
     * @return CollectionInterface<CollectionInterface<ElementInterface>>
     */
    public function getIndexes(): CollectionInterface;

    public function getGuidesProjectNode(): ProjectNode;

    public function getOutputFormat(): string;

    public function getOutputLocation(): string;

    /** @return Collection<DocumentInterface> */
    public function getDocuments(): Collection;

    public function getSource(): Source;

    public function addDocument(DocumentNode $document): void;
}
