<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Pipeline\Attribute\Stage;

#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    2000,
    'Filter empty namespaces',
)]
final class FilterEmptyNamespaces extends ApiDocumentationPass
{
    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        /** @var Collection<NamespaceInterface> $index */
        $index = $subject->getIndex('namespaces');
        $elementsIndex = $subject->getIndex('elements');
        $namespace = $subject->getNamespace();

        $this->checkForEmptyChildren($namespace, $namespace->getChildren(), $index, $elementsIndex);

        return $subject;
    }

    /**
     * @param Collection<NamespaceInterface> $namespaces
     * @param Collection<NamespaceInterface> $index
     * @param Collection<ElementInterface> $elementsIndex
     */
    private function checkForEmptyChildren(
        NamespaceInterface $namespace,
        Collection $namespaces,
        Collection $index,
        Collection $elementsIndex,
    ): void {
        foreach ($namespace->getChildren() as $childNamespace) {
            $this->checkForEmptyChildren($childNamespace, $namespace->getChildren(), $index, $elementsIndex);
        }

        if (! $namespace->isEmpty()) {
            return;
        }

        $index->offsetUnset((string) $namespace->getFullyQualifiedStructuralElementName());
        $namespaces->offsetUnset($namespace->getName());
        $elementsIndex->offsetUnset('~' . $namespace->getFullyQualifiedStructuralElementName());
    }
}
