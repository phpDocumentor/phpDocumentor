<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
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
    public function execute(ProjectDescriptor $project)
    {
        foreach ($project->getFiles() as $file) {
            $elements = array_merge(
                $file->getClasses()->getAll(),
                $file->getInterfaces()->getAll(),
                $file->getTraits()->getAll(),
                $file->getFunctions()->getAll(),
                $file->getConstants()->getAll()
            );

            /** @var DescriptorAbstract $element */
            foreach ($elements as $element) {
                $namespaceName      = $element->getNamespace();
                $namespaceIndexName = '~' . $namespaceName;

                if (!isset($project->getIndexes()->elements[$namespaceIndexName])) {
                    $parts = explode('\\', ltrim($namespaceName, '\\'));
                    $pointer = $project->getNamespace();
                    $fqnn = '';
                    foreach ($parts as $part) {
                        $fqnn .= '\\'.$part;
                        if ($pointer->getNamespaces()->get($part)) {
                            $pointer = $pointer->getNamespaces()->get($part);
                            continue;
                        }

                        // namespace does not exist, create it
                        $interimNamespaceDescriptor = new NamespaceDescriptor();
                        $interimNamespaceDescriptor->setName($part);
                        $interimNamespaceDescriptor->setFullyQualifiedStructuralElementName($fqnn);

                        // add to the pointer's list of children
                        $pointer->getNamespaces()->set($part, $interimNamespaceDescriptor);

                        // add to index
                        $project->getIndexes()->elements['~' . $fqnn] = $interimNamespaceDescriptor;

                        // move pointer forward
                        $pointer = $interimNamespaceDescriptor;
                    }
                }

                // replace textual representation with an object representation
                $element->setNamespace($project->getIndexes()->elements[$namespaceIndexName]);
            }
        }
    }
}
