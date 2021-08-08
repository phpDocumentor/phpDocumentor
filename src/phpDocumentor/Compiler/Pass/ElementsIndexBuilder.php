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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;

use function array_merge;
use function is_array;

/**
 * This class constructs the index 'elements' and populates it with all Structural Elements.
 *
 * Please note that due to a conflict between namespace FQSEN's and that of classes, interfaces, traits and functions
 * will the namespace FQSEN be prefixed with a tilde (~).
 */
class ElementsIndexBuilder implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 15000;

    public function getDescription(): string
    {
        return 'Build "elements" index';
    }

    public function execute(ProjectDescriptor $project): void
    {
        $elementCollection = new Collection();
        $project->getIndexes()->set('elements', $elementCollection);

        $constantsIndex  = $project->getIndexes()->fetch('constants', new Collection());
        $functionsIndex  = $project->getIndexes()->fetch('functions', new Collection());
        $classesIndex    = $project->getIndexes()->fetch('classes', new Collection());
        $interfacesIndex = $project->getIndexes()->fetch('interfaces', new Collection());
        $traitsIndex     = $project->getIndexes()->fetch('traits', new Collection());

        foreach ($project->getFiles() as $file) {
            $this->addElementsToIndexes($file->getConstants()->getAll(), [$constantsIndex, $elementCollection]);
            $this->addElementsToIndexes($file->getFunctions()->getAll(), [$functionsIndex, $elementCollection]);

            foreach ($file->getClasses()->getAll() as $element) {
                $this->addElementsToIndexes($element, [$classesIndex, $elementCollection]);
                $this->addElementsToIndexes($this->getSubElements($element), [$elementCollection]);
            }

            foreach ($file->getInterfaces()->getAll() as $element) {
                $this->addElementsToIndexes($element, [$interfacesIndex, $elementCollection]);
                $this->addElementsToIndexes($this->getSubElements($element), [$elementCollection]);
            }

            foreach ($file->getTraits()->getAll() as $element) {
                $this->addElementsToIndexes($element, [$traitsIndex, $elementCollection]);
                $this->addElementsToIndexes($this->getSubElements($element), [$elementCollection]);
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
     * @return DescriptorAbstract[]
     */
    protected function getSubElements(DescriptorAbstract $element): array
    {
        $subElements = [];

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
     * @param list<Collection<DescriptorAbstract>>    $indexes
     */
    protected function addElementsToIndexes($elements, array $indexes): void
    {
        if (!is_array($elements)) {
            $elements = [$elements];
        }

        /** @var DescriptorAbstract $element */
        foreach ($elements as $element) {
            /** @var Collection<DescriptorAbstract> $collection */
            foreach ($indexes as $collection) {
                $collection->set($this->getIndexKey($element), $element);
            }
        }
    }

    /**
     * Retrieves a key for the index for the provided element.
     */
    protected function getIndexKey(DescriptorAbstract $element): string
    {
        return (string) $element->getFullyQualifiedStructuralElementName();
    }
}
