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

namespace phpDocumentor\Guides;

use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Event\PostParseDocument;
use phpDocumentor\Guides\Nodes\DocumentNode;

final class DocumentCollector
{
    public function __construct(private readonly GuideSetDescriptor $guideSetDescriptor)
    {
    }

    public function __invoke(PostParseDocument $event): void
    {
        $this->addDocumentToDocumentationSet($event->getFileName(), $event->getDocumentNode());
    }

    private function addDocumentToDocumentationSet(
        string $file,
        DocumentNode $document,
    ): void {
        $this->guideSetDescriptor->addDocument(
            $document->getFilePath(),
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $file,
                $document->getTitle()?->getId() ?? '',
            ),
        );
    }
}
