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
use phpDocumentor\Descriptor\Interfaces\DocumentInterface;
use phpDocumentor\Descriptor\Interfaces\GuideDocumentationSet;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;

final class GuideSetDescriptor extends DocumentationSetDescriptor implements GuideDocumentationSet
{
    /** @var Collection<DocumentInterface> */
    private readonly Collection $documents;

    public function __construct(
        string $name,
        Source $source,
        string $outputLocation,
        private readonly string $inputFormat,
        private readonly ProjectNode $projectNode,
        private readonly string $outputFormat = 'html',
        private readonly int $initialHeaderLevel = 1,
    ) {
        parent::__construct();

        $this->name = $name;
        $this->source = $source;
        $this->outputLocation = $outputLocation;
        $this->documents = Collection::fromClassString(DocumentDescriptor::class);
    }

    public function addDocument(DocumentNode $document): void
    {
        $this->documents->set(
            $document->getFilePath(),
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $document->getFilePath(),
                $document->getTitle()?->getId() ?? '',
            ),
        );
    }

    public function getGuidesProjectNode(): ProjectNode
    {
        return $this->projectNode;
    }

    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    public function getInitialHeaderLevel(): int
    {
        return $this->initialHeaderLevel;
    }

    /** @return Collection<DocumentInterface> */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }
}
