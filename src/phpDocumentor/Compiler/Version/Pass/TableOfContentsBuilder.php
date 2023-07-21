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

namespace phpDocumentor\Compiler\Version\Pass;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\TocDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Guides\Nodes\DocumentTree\DocumentEntryNode;
use phpDocumentor\Guides\Nodes\DocumentTree\SectionEntryNode;
use phpDocumentor\Transformer\Router\Router;

use function ltrim;
use function sprintf;

final class TableOfContentsBuilder implements CompilerPassInterface
{
    public function __construct(private readonly Router $router)
    {
    }

    public function getDescription(): string
    {
        return 'Builds table of contents for api documentation sets';
    }

    public function __invoke(CompilableSubject $subject): CompilableSubject
    {
        if ($subject instanceof VersionDescriptor === false) {
            return $subject;
        }

        foreach ($subject->getDocumentationSets() as $documentationSet) {
            if ($documentationSet instanceof ApiSetDescriptor) {
                if ($documentationSet->getNamespace()->getChildren()->count() > 0) {
                    $namespacesToc = new TocDescriptor('Namespaces');
                    foreach ($documentationSet->getNamespace()->getChildren() as $child) {
                        $this->createNamespaceEntries($child, $namespacesToc);
                    }

                    $documentationSet->addTableOfContents($namespacesToc);
                }

                if ($documentationSet->getPackage()->getChildren()->count() > 0) {
                    $packagesToc = new TocDescriptor('Packages');
                    foreach ($documentationSet->getPackage()->getChildren() as $child) {
                        $this->createNamespaceEntries($child, $packagesToc);
                    }

                    $documentationSet->addTableOfContents($packagesToc);
                }
            }

            if (! ($documentationSet instanceof GuideSetDescriptor)) {
                continue;
            }

            $documents = $documentationSet->getDocuments();
            $index = $documents->fetch('index');
            if ($index === null) {
                continue;
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

        return $subject;
    }

    private function createNamespaceEntries(
        NamespaceInterface $namespace,
        TocDescriptor $namespacesToc,
        Entry|null $parent = null,
    ): void {
        $entry = new Entry(
            ltrim($this->router->generate($namespace), '/'),
            (string) $namespace->getFullyQualifiedStructuralElementName(),
            $parent?->getUrl(),
        );

        $parent?->addChild($entry);

        $namespacesToc->addEntry($entry);

        foreach ($namespace->getChildren() as $child) {
            $this->createNamespaceEntries($child, $namespacesToc, $entry);
        }
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
