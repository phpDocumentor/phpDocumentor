<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Event\PostParseDocument;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\TitleNode;

final class DocumentCollector
{
    public function __construct(private readonly GuideSetDescriptor $guideSetDescriptor)
    {
    }

    public function __invoke(PostParseDocument $event): void
    {
        if (! ($event->getDocumentNode()->getTitle() instanceof TitleNode)) {
            return;
        }

        $this->addDocumentToDocumentationSet($event->getFileName(), $event->getDocumentNode());
    }

    private function addDocumentToDocumentationSet(
        string $file,
        DocumentNode $document,
    ): void {
        $this->guideSetDescriptor->addDocument(
            $file,
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $file,
                $document->getTitle()->getId(),
            ),
        );
    }
}
