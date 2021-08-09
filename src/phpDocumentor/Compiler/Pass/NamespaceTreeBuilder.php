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

use InvalidArgumentException;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
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
class NamespaceTreeBuilder implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 9000;

    public function getDescription(): string
    {
        return 'Build "namespaces" index and add namespaces to "elements"';
    }

    public function execute(ProjectDescriptor $project): void
    {
        $project->getIndexes()->fetch('elements', new Collection())->set('~\\', $project->getNamespace());
        $project->getIndexes()->fetch('namespaces', new Collection())->set('\\', $project->getNamespace());

        foreach ($project->getFiles() as $file) {
            $this->addElementsOfTypeToNamespace($project, $file->getConstants()->getAll(), 'constants');
            $this->addElementsOfTypeToNamespace($project, $file->getFunctions()->getAll(), 'functions');
            $this->addElementsOfTypeToNamespace($project, $file->getClasses()->getAll(), 'classes');
            $this->addElementsOfTypeToNamespace($project, $file->getInterfaces()->getAll(), 'interfaces');
            $this->addElementsOfTypeToNamespace($project, $file->getTraits()->getAll(), 'traits');
        }

        /** @var NamespaceDescriptor $namespace */
        foreach ($project->getIndexes()->get('namespaces')->getAll() as $namespace) {
            if ($namespace->getNamespace() === '') {
                continue;
            }

            $this->addToParentNamespace($project, $namespace);
        }
    }

    /**
     * Adds the given elements of a specific type to their respective Namespace Descriptors.
     *
     * This method will assign the given elements to the namespace as registered in the namespace field of that
     * element. If a namespace does not exist yet it will automatically be created.
     *
     * @param DescriptorAbstract[] $elements Series of elements to add to their respective namespace.
     * @param string               $type     Declares which field of the namespace will be populated with the given
     *                                       series of elements. This name will be transformed to a getter which must
     *                                       exist. Out of performance considerations will no effort be done to verify
     *                                       whether the provided type is valid.
     */
    protected function addElementsOfTypeToNamespace(ProjectDescriptor $project, array $elements, string $type): void
    {
        foreach ($elements as $element) {
            $namespaceName = (string) $element->getNamespace();
            //TODO: find out why this can happen. Some bug in the assembler?
            if ($namespaceName === '') {
                $namespaceName = '\\';
            }

            $namespace = $project->getIndexes()->fetch('namespaces', new Collection())->fetch($namespaceName);

            if ($namespace === null) {
                $namespace = new NamespaceDescriptor();
                $fqsen     = new Fqsen($namespaceName);
                $namespace->setName($fqsen->getName());
                $namespace->setFullyQualifiedStructuralElementName($fqsen);
                $namespaceName = substr((string) $fqsen, 0, -strlen($fqsen->getName()) - 1);
                $namespace->setNamespace($namespaceName);
                $project->getIndexes()
                    ->fetch('namespaces', new Collection())
                    ->set((string) $namespace->getFullyQualifiedStructuralElementName(), $namespace);
                $this->addToParentNamespace($project, $namespace);
            }

            Assert::isInstanceOf($namespace, NamespaceDescriptor::class);

            // replace textual representation with an object representation
            $element->setNamespace($namespace);

            // add element to namespace
            $getter = 'get' . ucfirst($type);

            /** @var Collection<DescriptorAbstract> $collection */
            $collection = $namespace->{$getter}();
            $collection->add($element);
        }
    }

    private function addToParentNamespace(ProjectDescriptor $project, NamespaceDescriptor $namespace): void
    {
        /** @var NamespaceDescriptor|null $parent */
        $parent = $project->getIndexes()->fetch(
            'namespaces',
            new Collection()
        )->fetch((string) $namespace->getNamespace());
        $project->getIndexes()->fetch('elements', new Collection())->set(
            '~' . (string) $namespace->getFullyQualifiedStructuralElementName(),
            $namespace
        );

        try {
            if ($parent === null) {
                $parent = new NamespaceDescriptor();
                $fqsen  = new Fqsen($namespace->getNamespace());
                $parent->setFullyQualifiedStructuralElementName($fqsen);
                $parent->setName($fqsen->getName());
                $namespaceName = substr((string) $fqsen, 0, -strlen($parent->getName()) - 1);
                $parent->setNamespace($namespaceName === '' ? '\\' : $namespaceName);
                $project->getIndexes()
                    ->fetch('namespaces', new Collection())
                    ->set((string) $parent->getFullyQualifiedStructuralElementName(), $parent);
                $this->addToParentNamespace($project, $parent);
            }

            $namespace->setParent($parent);
            $parent->getChildren()->set($namespace->getName(), $namespace);
        } catch (InvalidArgumentException $e) {
            //bit hacky but it works for now.
            //$project->getNamespace()->getChildren()->add($namespace);
        }
    }
}
