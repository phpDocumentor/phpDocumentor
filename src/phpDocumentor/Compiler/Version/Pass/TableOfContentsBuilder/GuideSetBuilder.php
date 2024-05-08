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

namespace phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\TocDescriptor;
use phpDocumentor\Guides\Nodes\DocumentTree\DocumentEntryNode;
use phpDocumentor\Guides\Nodes\DocumentTree\SectionEntryNode;
use phpDocumentor\Transformer\Router\Router;

use function ltrim;
use function sprintf;

/** @implements DocumentationSetBuilder<GuideSetDescriptor> */
final class GuideSetBuilder implements DocumentationSetBuilder
{
    public function __construct(private readonly Router $router)
    {
    }

    public function supports(DocumentationSetDescriptor $documentationSet): bool
    {
        return $documentationSet instanceof GuideSetDescriptor;
    }

    public function build(DocumentationSetDescriptor $documentationSet): void
    {
        $documents = $documentationSet->getDocuments();
        $index = $documents->fetch('index');
        if ($index === null) {
            return;
        }

        $guideToc = new TocDescriptor($index->getTitle());
        $this->createGuideEntries(
            $index,
            $documentationSet->getGuidesProjectNode()->findDocumentEntry($index->getFile()),
            $documentationSet,
            $guideToc,
        );

        $documentationSet->addTableOfContents($guideToc);
    }

    private function createGuideEntries(
        DocumentDescriptor $documentDescriptor,
        DocumentEntryNode|SectionEntryNode $metaEntry,
        GuideSetDescriptor $guideSetDescriptor,
        TocDescriptor $guideToc,
        Entry|null $parent = null,
    ): void {
        $projectNode = $guideSetDescriptor->getGuidesProjectNode();

        foreach ($metaEntry->getChildren() as $metaChild) {
            if ($metaChild instanceof DocumentEntryNode) {
                $refMetaData = $projectNode->findDocumentEntry(ltrim($metaChild->getFile(), '/'));
                if ($refMetaData !== null) {
                    $refDocument = $guideSetDescriptor->getDocuments()->get($refMetaData->getFile());
                    $entry = new Entry(
                        sprintf(
                            '%s/%s#%s',
                            $guideSetDescriptor->getOutputLocation(),
                            ltrim($this->router->generate($refDocument), '/'),
                            $refMetaData->getTitle()->getId(),
                        ),
                        $refMetaData->getTitle()->toString(),
                        $parent?->getUrl(),
                    );

                    $parent?->addChild($entry);

                    $guideToc->addEntry($entry);

                    if ($refDocument->getFile() === $documentDescriptor->getFile()) {
                        continue;
                    }

                    $this->createGuideEntries($refDocument, $refMetaData, $guideSetDescriptor, $guideToc, $entry);
                }
            }

            if (! ($metaChild instanceof SectionEntryNode)) {
                continue;
            }

            if ($metaChild->getTitle()->getId() === $documentDescriptor->getDocumentNode()->getTitle()->getId()) {
                $this->createGuideEntries($documentDescriptor, $metaChild, $guideSetDescriptor, $guideToc, $parent);
                continue;
            }

            $entry = new Entry(
                ltrim($this->router->generate($documentDescriptor), '/')
                . '#' . $metaChild->getTitle()->getId(),
                $metaChild->getTitle()->toString(),
                $parent?->getUrl(),
            );

            $parent?->addChild($entry);

            $guideToc->addEntry($entry);

            $this->createGuideEntries($documentDescriptor, $metaChild, $guideSetDescriptor, $guideToc, $entry);
        }
    }
}
