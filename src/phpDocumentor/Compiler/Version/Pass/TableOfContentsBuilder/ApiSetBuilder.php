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

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\TocDescriptor;
use phpDocumentor\Transformer\Router\Router;

use function ltrim;

/** @implements DocumentationSetBuilder<ApiSetDescriptor> */
final class ApiSetBuilder implements DocumentationSetBuilder
{
    public function __construct(private readonly Router $router)
    {
    }

    public function supports(DocumentationSetDescriptor $documentationSet): bool
    {
        return $documentationSet instanceof ApiSetDescriptor;
    }

    public function build(DocumentationSetDescriptor $documentationSet): void
    {
        if ($documentationSet->getNamespace()->getChildren()->count() > 0) {
            $namespacesToc = new TocDescriptor('Namespaces');
            foreach ($documentationSet->getNamespace()->getChildren() as $child) {
                $this->createNamespaceEntries($child, $namespacesToc);
            }

            $documentationSet->addTableOfContents($namespacesToc);
        }

        if ($documentationSet->getPackage()->getChildren()->count() <= 0) {
            return;
        }

        $packagesToc = new TocDescriptor('Packages');
        foreach ($documentationSet->getPackage()->getChildren() as $child) {
            $this->createNamespaceEntries($child, $packagesToc);
        }

        $documentationSet->addTableOfContents($packagesToc);
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
}
