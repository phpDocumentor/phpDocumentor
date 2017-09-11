<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\Fqsen;

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
    const COMPILER_PRIORITY = 9000;

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Build "namespaces" index and add namespaces to "elements"';
    }

    /**
     * {@inheritDoc}
     */
    public function execute(ProjectDescriptor $project)
    {
        $project->getIndexes()->get('elements', new Collection())->set('~\\', $project->getNamespace());
        $project->getIndexes()->get('namespaces', new Collection())->set('\\', $project->getNamespace());

        foreach ($project->getFiles() as $file) {
            $this->addElementsOfTypeToNamespace($project, $file->getConstants()->getAll(), 'constants');
            $this->addElementsOfTypeToNamespace($project, $file->getFunctions()->getAll(), 'functions');
            $this->addElementsOfTypeToNamespace($project, $file->getClasses()->getAll(), 'classes');
            $this->addElementsOfTypeToNamespace($project, $file->getInterfaces()->getAll(), 'interfaces');
            $this->addElementsOfTypeToNamespace($project, $file->getTraits()->getAll(), 'traits');
        }

        /** @var NamespaceDescriptor $namespace */
        foreach ($project->getIndexes()->get('namespaces')->getAll() as $namespace) {
            if ($namespace->getNamespace() !== '') {
                $this->addToParentNamespace($project, $namespace);

            }
        }
    }

    /**
     * Adds the given elements of a specific type to their respective Namespace Descriptors.
     *
     * This method will assign the given elements to the namespace as registered in the namespace field of that
     * element. If a namespace does not exist yet it will automatically be created.
     *
     * @param ProjectDescriptor $project
     * @param DescriptorAbstract[] $elements Series of elements to add to their respective namespace.
     * @param string $type Declares which field of the namespace will be populated with the given
     * series of elements. This name will be transformed to a getter which must exist. Out of performance
     * considerations will no effort be done to verify whether the provided type is valid.
     *
     * @return void
     */
    protected function addElementsOfTypeToNamespace(ProjectDescriptor $project, array $elements, $type)
    {
        /** @var DescriptorAbstract $element */
        foreach ($elements as $element) {
            $namespaceName = (string)$element->getNamespace();
            //TODO: find out why this can happen. Some bug in the assembler?
            if ($namespaceName === '') {
                $namespaceName = '\\';
            }

            $namespace = $project->getIndexes()->get('namespaces', new Collection())->get($namespaceName);

            if ($namespace === null) {
                $namespace = new NamespaceDescriptor();
                $namespace->setName($namespaceName);
                $namespace->setFullyQualifiedStructuralElementName(new Fqsen($namespaceName));
                $project->getIndexes()->get('namespaces')->set($namespaceName, $namespace);
            }

            // replace textual representation with an object representation
            $element->setNamespace($namespace);

            // add element to namespace
            $getter = 'get' . ucfirst($type);

            /** @var Collection $collection */
            $collection = $namespace->$getter();
            $collection->add($element);

        }
    }

    /**
     * @param ProjectDescriptor $project
     * @param $namespace
     */
    private function addToParentNamespace(ProjectDescriptor $project, NamespaceDescriptor $namespace)
    {
        /** @var NamespaceDescriptor $parent */
        $parent = $project->getIndexes()->get('namespaces')->get($namespace->getNamespace());

        try {
            if ($parent === null) {
                $parent = new NamespaceDescriptor();
                $fqsen = new Fqsen($namespace->getNamespace());
                $parent->setFullyQualifiedStructuralElementName($fqsen);
                $parent->setName($fqsen->getName());
                $namespaceName = substr((string)$fqsen, 0, -strlen($parent->getName())-1);
                $parent->setNamespace($namespaceName === '' ? '\\' : $namespaceName);
                $project->getIndexes()->get('namespaces')->set($namespace->getNamespace(), $namespace);
                $this->addToParentNamespace($project, $parent);
            }

            $namespace->setParent($parent);
            $parent->getChildren()->add($namespace);
        } catch (\InvalidArgumentException $e) {
            //bit hacky but it works for now.
            //$project->getNamespace()->getChildren()->add($namespace);
        }
    }
}
