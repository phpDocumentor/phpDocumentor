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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use InvalidArgumentException;
use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Pipeline\Attribute\Stage;
use phpDocumentor\Reflection\Fqsen;
use Webmozart\Assert\Assert;

use function strlen;
use function substr;
use function ucfirst;

/**
 * Rebuilds the namespace tree from the elements found in files.
 *
 * On every compiler pass is the namespace tree rebuild to aid in the process
 * of incremental updates. The Files Collection in the Project Descriptor is the
 * only location where aliases to elements may be serialized.
 *
 * If the namespace tree were to be persisted then both locations needed to be
 * invalidated if a file were to change.
 */
#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    9000,
    'Build "namespaces" index and add namespaces to "elements"',
)]
final class NamespaceTreeBuilder extends ApiDocumentationPass
{
    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        $subject->getIndexes()
            ->fetch('elements', new Collection())
            ->set('~\\', $subject->getNamespace());
        $subject->getIndexes()
            ->fetch('namespaces', new Collection())
            ->set('\\', $subject->getNamespace());

        foreach ($subject->getFiles() as $file) {
            $this->addElementsOfTypeToNamespace($subject, $file->getConstants()->getAll(), 'constants');
            $this->addElementsOfTypeToNamespace($subject, $file->getFunctions()->getAll(), 'functions');
            $this->addElementsOfTypeToNamespace($subject, $file->getClasses()->getAll(), 'classes');
            $this->addElementsOfTypeToNamespace($subject, $file->getInterfaces()->getAll(), 'interfaces');
            $this->addElementsOfTypeToNamespace($subject, $file->getTraits()->getAll(), 'traits');
            $this->addElementsOfTypeToNamespace($subject, $file->getEnums()->getAll(), 'enums');
        }

        /** @var NamespaceInterface $namespace */
        foreach ($subject->getIndexes()->get('namespaces')->getAll() as $namespace) {
            if ($namespace->getNamespace() === '') {
                continue;
            }

            $this->addToParentNamespace($subject, $namespace);
        }

        return $subject;
    }

    /**
     * Adds the given elements of a specific type to their respective Namespace Descriptors.
     *
     * This method will assign the given elements to the namespace as registered in the namespace field of that
     * element. If a namespace does not exist yet it will automatically be created.
     *
     * @param ElementInterface[] $elements Series of elements to add to their respective namespace.
     * @param string $type Declares which field of the namespace will be populated with the given series of elements.
     *             This name will be transformed to a getter which must exist.
     *             Out of performance considerations will no effort
     *             be done to verify whether the provided type is valid.
     */
    protected function addElementsOfTypeToNamespace(
        DocumentationSetDescriptor $documentationSet,
        array $elements,
        string $type,
    ): void {
        foreach ($elements as $element) {
            $namespaceName = (string) $element->getNamespace();
            //TODO: find out why this can happen. Some bug in the assembler?
            if ($namespaceName === '') {
                $namespaceName = '\\';
            }

            $namespace = $documentationSet->getIndexes()->fetch('namespaces', new Collection())->fetch($namespaceName);

            if ($namespace === null) {
                $namespace = new NamespaceDescriptor();
                $fqsen     = new Fqsen($namespaceName);
                $namespace->setName($fqsen->getName());
                $namespace->setFullyQualifiedStructuralElementName($fqsen);
                $namespaceName = substr((string) $fqsen, 0, -strlen($fqsen->getName()) - 1);
                $namespace->setNamespace($namespaceName);
                $documentationSet->getIndexes()
                    ->fetch('namespaces', new Collection())
                    ->set((string) $namespace->getFullyQualifiedStructuralElementName(), $namespace);
                $this->addToParentNamespace($documentationSet, $namespace);
            }

            Assert::isInstanceOf($namespace, NamespaceInterface::class);

            // replace textual representation with an object representation
            $element->setNamespace($namespace);

            // add element to namespace
            $getter = 'get' . ucfirst($type);

            /** @var Collection<ElementInterface> $collection */
            $collection = $namespace->{$getter}();
            $collection->add($element);
        }
    }

    private function addToParentNamespace(
        DocumentationSetDescriptor $documentationSet,
        NamespaceInterface $namespace,
    ): void {
        /** @var NamespaceInterface|null $parent */
        $parent = $documentationSet->getIndexes()->fetch(
            'namespaces',
            new Collection(),
        )->fetch((string) $namespace->getNamespace());
        $documentationSet->getIndexes()->fetch('elements', new Collection())->set(
            '~' . (string) $namespace->getFullyQualifiedStructuralElementName(),
            $namespace,
        );

        try {
            if ($parent === null) {
                $parent = new NamespaceDescriptor();
                $fqsen  = new Fqsen($namespace->getNamespace());
                $parent->setFullyQualifiedStructuralElementName($fqsen);
                $parent->setName($fqsen->getName());
                $namespaceName = substr((string) $fqsen, 0, -strlen($parent->getName()) - 1);
                $parent->setNamespace($namespaceName === '' ? '\\' : $namespaceName);
                $documentationSet->getIndexes()
                    ->fetch('namespaces', new Collection())
                    ->set((string) $parent->getFullyQualifiedStructuralElementName(), $parent);
                $this->addToParentNamespace($documentationSet, $parent);
            }

            $namespace->setParent($parent);
            $parent->getChildren()->set($namespace->getName(), $namespace);
        } catch (InvalidArgumentException) {
            //bit hacky but it works for now.
            //$project->getNamespace()->getChildren()->add($namespace);
        }
    }
}
