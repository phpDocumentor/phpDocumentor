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
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;

/**
 * This class constructs the index 'elements' and populates it with all Structural Elements.
 *
 * Please note that due to a conflict between namespace FQSEN's and that of classes, interfaces, traits and functions
 * will the namespace FQSEN be prefixed with a tilde (~).
 */
class ElementsIndexBuilder implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 15000;

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Build "elements" index';
    }

    /**
     * {@inheritDoc}
     */
    public function execute(ProjectDescriptor $project)
    {
        $elementCollection = new Collection();
        $project->getIndexes()->set('elements', $elementCollection);

        $constantsIndex  = $project->getIndexes()->get('constants', new Collection());
        $functionsIndex  = $project->getIndexes()->get('functions', new Collection());
        $classesIndex    = $project->getIndexes()->get('classes', new Collection());
        $interfacesIndex = $project->getIndexes()->get('interfaces', new Collection());
        $traitsIndex     = $project->getIndexes()->get('traits', new Collection());

        foreach ($project->getFiles() as $file) {
            $this->addElementsToIndexes($file->getConstants()->getAll(), array($constantsIndex, $elementCollection));
            $this->addElementsToIndexes($file->getFunctions()->getAll(), array($functionsIndex, $elementCollection));

            foreach ($file->getClasses()->getAll() as $element) {
                $this->addElementsToIndexes($element, array($classesIndex, $elementCollection));
                $this->addElementsToIndexes($this->getSubElements($element), array($elementCollection));
            }

            foreach ($file->getInterfaces()->getAll() as $element) {
                $this->addElementsToIndexes($element, array($interfacesIndex, $elementCollection));
                $this->addElementsToIndexes($this->getSubElements($element), array($elementCollection));
            }

            foreach ($file->getTraits()->getAll() as $element) {
                $this->addElementsToIndexes($element, array($traitsIndex, $elementCollection));
                $this->addElementsToIndexes($this->getSubElements($element), array($elementCollection));
            }
        }
    }

    /**
     * Returns any sub-elements for the given element.
     *
     * This method checks whether the given element is a class, interface or trait and returns
     * their methods, properties and constants accordingly, or an empty array if no sub-elements
     * are applicable.
     *
     * @param DescriptorAbstract $element
     *
     * @return DescriptorAbstract[]
     */
    protected function getSubElements(DescriptorAbstract $element)
    {
        $subElements = array();

        if ($element instanceof ClassInterface) {
            $subElements = array_merge(
                $element->getMethods()->getAll(),
                $element->getConstants()->getAll(),
                $element->getProperties()->getAll()
            );
        }

        if ($element instanceof InterfaceInterface) {
            $subElements = array_merge(
                $element->getMethods()->getAll(),
                $element->getConstants()->getAll()
            );
        }

        if ($element instanceof TraitInterface) {
            $subElements = array_merge(
                $element->getMethods()->getAll(),
                $element->getProperties()->getAll()
            );
        }

        return $subElements;
    }

    /**
     * Adds a series of descriptors to the given list of collections.
     *
     * @param DescriptorAbstract|DescriptorAbstract[] $elements
     * @param Collection[]                            $indexes
     *
     * @return void
     */
    protected function addElementsToIndexes($elements, $indexes)
    {
        if (!is_array($elements)) {
            $elements = array($elements);
        }

        /** @var DescriptorAbstract $element */
        foreach ($elements as $element) {
            /** @var Collection $collection */
            foreach ($indexes as $collection) {
                $collection->set($this->getIndexKey($element), $element);
            }
        }
    }

    /**
     * Retrieves a key for the index for the provided element.
     *
     * @param DescriptorAbstract $element
     *
     * @return string
     */
    protected function getIndexKey($element)
    {
        $key = $element->getFullyQualifiedStructuralElementName();

        // properties should have an additional $ before the property name
        if ($element instanceof PropertyInterface) {
            list($fqcn, $propertyName) = explode('::', $key);
            $key = $fqcn . '::$' . $propertyName;
        }

        return $key;
    }
}
