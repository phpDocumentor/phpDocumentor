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

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Pipeline\Attribute\Stage;

use function array_merge;
use function is_array;

/**
 * This class constructs the index 'elements' and populates it with all Structural Elements.
 *
 * Please note that due to a conflict between namespace FQSEN's and that of classes, interfaces, traits and functions
 * will the namespace FQSEN be prefixed with a tilde (~).
 */
#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    15000,
    'Build "elements" index',
)]
final class ElementsIndexBuilder extends ApiDocumentationPass
{
    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        $elementCollection = new Collection();
        $subject->getIndexes()->set('elements', $elementCollection);

        $constantsIndex  = $subject->getIndexes()->fetch('constants', new Collection());
        $functionsIndex  = $subject->getIndexes()->fetch('functions', new Collection());
        $classesIndex    = $subject->getIndexes()->fetch('classes', new Collection());
        $interfacesIndex = $subject->getIndexes()->fetch('interfaces', new Collection());
        $traitsIndex     = $subject->getIndexes()->fetch('traits', new Collection());
        $enumsIndex     = $subject->getIndexes()->fetch('enums', new Collection());

        foreach ($subject->getFiles() as $file) {
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

            foreach ($file->getEnums()->getAll() as $element) {
                $this->addElementsToIndexes($element, [$enumsIndex, $elementCollection]);
                $this->addElementsToIndexes($this->getSubElements($element), [$elementCollection]);
            }
        }

        return $subject;
    }

    /**
     * Returns any sub-elements for the given element.
     *
     * This method checks whether the given element is a class, interface or trait and returns
     * their methods, properties and constants accordingly, or an empty array if no sub-elements
     * are applicable.
     *
     * @return ElementInterface[]
     */
    protected function getSubElements(ElementInterface $element): array
    {
        $subElements = [];

        if ($element instanceof ClassInterface) {
            $subElements = array_merge(
                $element->getMethods()->getAll(),
                $element->getConstants()->getAll(),
                $element->getProperties()->getAll(),
            );
        }

        if ($element instanceof InterfaceInterface) {
            $subElements = array_merge(
                $element->getMethods()->getAll(),
                $element->getConstants()->getAll(),
            );
        }

        if ($element instanceof TraitInterface) {
            $subElements = array_merge(
                $element->getMethods()->getAll(),
                $element->getProperties()->getAll(),
            );
        }

        if ($element instanceof EnumInterface) {
            $subElements = array_merge(
                $element->getMethods()->getAll(),
                $element->getCases()->getAll(),
            );
        }

        return $subElements;
    }

    /**
     * Adds a series of descriptors to the given list of collections.
     *
     * @param ElementInterface|ElementInterface[] $elements
     * @param list<Collection<ElementInterface>> $indexes
     */
    protected function addElementsToIndexes($elements, array $indexes): void
    {
        if (! is_array($elements)) {
            $elements = [$elements];
        }

        /** @var ElementInterface $element */
        foreach ($elements as $element) {
            /** @var Collection<ElementInterface> $collection */
            foreach ($indexes as $collection) {
                $collection->set($this->getIndexKey($element), $element);
            }
        }
    }

    /**
     * Retrieves a key for the index for the provided element.
     */
    protected function getIndexKey(ElementInterface $element): string
    {
        return (string) $element->getFullyQualifiedStructuralElementName();
    }
}
