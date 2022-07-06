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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\TocDescriptor;
use phpDocumentor\Transformer\Router\Router;
use Psr\Log\LoggerInterface;

use function ltrim;
use function sprintf;

final class TableOfContentsBuilder implements CompilerPassInterface
{
    /** @var Router */
    private $router;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(Router $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    public function getDescription(): string
    {
        return 'Builds table of contents for api documentation sets';
    }

    public function execute(DocumentationSetDescriptor $project): void
    {
        if ($project instanceof ApiSetDescriptor) {
            if ($project->getNamespace()->getChildren()->count() > 0) {
                $namespacesToc = new TocDescriptor('Namespaces');
                foreach ($project->getNamespace()->getChildren() as $child) {
                    $this->createNamespaceEntries($child, $namespacesToc);
                }

                $project->addTableOfContents($namespacesToc);
            }

            if ($project->getPackage()->getChildren()->count() > 0) {
                $packagesToc = new TocDescriptor('Packages');
                foreach ($project->getPackage()->getChildren() as $child) {
                    $this->createNamespaceEntries($child, $packagesToc);
                }

                $project->addTableOfContents($packagesToc);
            }
        }

        if (!($project instanceof GuideSetDescriptor)) {
            return;
        }

        $documents = $project->getDocuments();
        $index     = $documents->fetch('index');
        if ($index === null) {
            return;
        }

        $guideToc = new TocDescriptor($index->getTitle());
        $this->createGuideEntries($index, $documents, $guideToc);

        $project->addTableOfContents($guideToc);
    }

    private function createNamespaceEntries(
        NamespaceDescriptor $namespace,
        TocDescriptor $namespacesToc,
        ?Entry $parent = null
    ): void {
        $entry = new Entry(
            ltrim($this->router->generate($namespace), '/'),
            (string) $namespace->getFullyQualifiedStructuralElementName(),
            $parent !== null ? $parent->getUrl() : null
        );

        if ($parent !== null) {
            $parent->addChild($entry);
        }

        $namespacesToc->addEntry($entry);

        foreach ($namespace->getChildren() as $child) {
            $this->createNamespaceEntries($child, $namespacesToc, $entry);
        }
    }

    /** @param Collection<DocumentDescriptor> $documents */
    private function createGuideEntries(
        DocumentDescriptor $documentDescriptor,
        Collection $documents,
        TocDescriptor $guideToc,
        ?Entry $parent = null
    ): void {
        foreach ($documentDescriptor->getTocs() as $toc) {
            foreach ($toc->getFiles() as $file) {
                $subDocument = $documents->fetch(ltrim($file, '/'));
                if ($subDocument === null) {
                    $this->logger->error(sprintf('Toc contains a link to a missing document %s', $file));
                    continue;
                }

                $entry = new Entry(
                    'guide/' . ltrim($this->router->generate($subDocument), '/'),
                    $subDocument->getTitle(),
                    $parent !== null ? $parent->getUrl() : null
                );

                if ($parent !== null) {
                    $parent->addChild($entry);
                }

                $guideToc->addEntry($entry);

                if ($subDocument->getFile() === $documentDescriptor->getFile()) {
                    continue;
                }

                $this->createGuideEntries($subDocument, $documents, $guideToc, $entry);
            }
        }
    }
}
