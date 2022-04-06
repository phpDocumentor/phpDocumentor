<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Event\PostParseDocument;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\Nodes\DocumentNode;
use Psr\Log\LoggerInterface;

use function sprintf;

final class DocumentCollector
{
    private Metas $metas;
    private GuideSetDescriptor $guideSetDescriptor;
    private LoggerInterface $logger;

    public function __construct(Metas $metas, GuideSetDescriptor $guideSetDescriptor, LoggerInterface $logger)
    {
        $this->metas = $metas;
        $this->guideSetDescriptor = $guideSetDescriptor;
        $this->logger = $logger;
    }

    public function __invoke(PostParseDocument $event): void
    {
        $this->addDocumentToDocumentationSet($event->getFileName(), $event->getDocumentNode());
    }

    private function addDocumentToDocumentationSet(
        string $file,
        DocumentNode $document
    ): void {
        $metaEntry = $this->metas->get($file);
        if ($metaEntry instanceof Entry === false) {
            $this->logger->error(sprintf('Could not find meta entry for %s, parsing may have failed', $file));

            return;
        }

        $this->guideSetDescriptor->addDocument(
            $file,
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $file,
                $document->getTitle() ? $document->getTitle()->getValueString() : '',
                $document->getTitles(),
                $document->getTocs(),
                $document->getDependencies()
            )
        );
    }
}
