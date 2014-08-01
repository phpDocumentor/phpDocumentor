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
        $project->getIndexes()->get('namespaces', new Collection())->add($project->getNamespace());

        foreach ($project->getFiles() as $file) {
            $this->addElementsOfTypeToNamespace($project, $file->getConstants()->getAll(), 'constants');
            $this->addElementsOfTypeToNamespace($project, $file->getFunctions()->getAll(), 'functions');
            $this->addElementsOfTypeToNamespace($project, $file->getClasses()->getAll(), 'classes');
            $this->addElementsOfTypeToNamespace($project, $file->getInterfaces()->getAll(), 'interfaces');
            $this->addElementsOfTypeToNamespace($project, $file->getTraits()->getAll(), 'traits');
        }
    }

    /**
     * Adds the given elements of a specific type to their respective Namespace Descriptors.
     *
     * This method will assign the given elements to the namespace as registered in the namespace field of that
     * element. If a namespace does not exist yet it will automatically be created.
     *
     * @param ProjectDescriptor    $project
     * @param DescriptorAbstract[] $elements Series of elements to add to their respective namespace.
     * @param string               $type     Declares which field of the namespace will be populated with the given
     * series of elements. This name will be transformed to a getter which must exist. Out of performance
     * considerations will no effort be done to verify whether the provided type is valid.
     *
     * @return void
     */
    protected function addElementsOfTypeToNamespace(ProjectDescriptor $project, array $elements, $type)
    {
        /** @var DescriptorAbstract $element */
        foreach ($elements as $element) {
            $namespaceName = (string) $element->getNamespace();

            // ensure consistency by trimming the slash prefix and then re-appending it.
            $namespaceIndexName = '~\\' . ltrim($namespaceName, '\\');

            if (!isset($project->getIndexes()->elements[$namespaceIndexName])) {
                $this->createNamespaceDescriptorTree($project, $namespaceName);
            }

            /** @var NamespaceDescriptor $namespace */
            $namespace = $project->getIndexes()->elements[$namespaceIndexName];

            // replace textual representation with an object representation
            $element->setNamespace($namespace);

            // add element to namespace
            $getter = 'get'.ucfirst($type);

            /** @var Collection $collection  */
            $collection = $namespace->$getter();
            $collection->add($element);
        }
    }

    /**
     * Creates a tree of NamespaceDescriptors based on the provided FQNN (namespace name).
     *
     * This method will examine the namespace name and create a namespace descriptor for each part of
     * the FQNN if it doesn't exist in the namespaces field of the current namespace (starting with the root
     * Namespace in the Project Descriptor),
     *
     * As an intended side effect this method also populates the *elements* index of the ProjectDescriptor with all
     * created NamespaceDescriptors. Each index key is prefixed with a tilde (~) so that it will not conflict with
     * other FQSEN's, such as classes or interfaces.
     *
     * @param ProjectDescriptor $project
     * @param string            $namespaceName A FQNN of the namespace (and parents) to create.
     *
     * @see ProjectDescriptor::getNamespace() for the root namespace.
     * @see NamespaceDescriptor::getNamespaces() for the child namespaces of a given namespace.
     *
     * @return void
     */
    protected function createNamespaceDescriptorTree(ProjectDescriptor $project, $namespaceName)
    {
        $parts   = explode('\\', ltrim($namespaceName, '\\'));
        $fqnn    = '';

        // this method does not use recursion to traverse the tree but uses a pointer that will be overridden with the
        // next item that is to be traversed (child namespace) at the end of the loop.
        $pointer = $project->getNamespace();
        foreach ($parts as $part) {
            $fqnn .= '\\' . $part;
            if ($pointer->getChildren()->get($part)) {
                $pointer = $pointer->getChildren()->get($part);
                continue;
            }

            // namespace does not exist, create it
            $interimNamespaceDescriptor = new NamespaceDescriptor();
            $interimNamespaceDescriptor->setParent($pointer);
            $interimNamespaceDescriptor->setName($part);
            $interimNamespaceDescriptor->setFullyQualifiedStructuralElementName($fqnn);

            // add to the pointer's list of children
            $pointer->getChildren()->set($part, $interimNamespaceDescriptor);

            // add to index
            $project->getIndexes()->elements['~' . $fqnn] = $interimNamespaceDescriptor;
            $project->getIndexes()->get('namespaces', new Collection())->add($interimNamespaceDescriptor);

            // move pointer forward
            $pointer = $interimNamespaceDescriptor;
        }
    }
}
