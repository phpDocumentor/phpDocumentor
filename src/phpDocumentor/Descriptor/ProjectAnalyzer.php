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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use Stringable;

use function count;
use function is_string;
use function sprintf;
use function str_replace;

use const PHP_EOL;

/**
 * Analyzes a Project Descriptor and collects key information.
 *
 * This class can be used by external tools to analyze the Project Descriptor and collect key information from it such
 * as the total number of elements per type of Descriptor, the number of top level namespaces or the number of parent
 * classes that could not be interpreted by the Compiler passes.
 */
class ProjectAnalyzer implements Stringable
{
    protected int $fileCount = 0;
    protected int $topLevelNamespaceCount = 0;
    protected int $unresolvedParentClassesCount = 0;

    /** @var array<array-key, int> $descriptorCountByType */
    protected array $descriptorCountByType = [];

    /**
     * Analyzes the given project descriptor and populates this object's properties.
     */
    public function analyze(DocumentationSetDescriptor $documentationSet): void
    {
        $this->unresolvedParentClassesCount = 0;

        $elementCounter = [];
        foreach ($this->findAllElements($documentationSet) as $element) {
            $elementCounter = $this->addElementToCounter($elementCounter, $element);
            $this->incrementUnresolvedParentCounter($element);
        }

        $this->descriptorCountByType = $elementCounter;
        $this->fileCount = count($documentationSet->getFiles());

        if (! $documentationSet instanceof ApiSetDescriptor) {
            return;
        }

        $this->topLevelNamespaceCount = count($documentationSet->getNamespace()->getChildren());
    }

    /**
     * Returns a textual report of the findings of this class.
     */
    public function __toString(): string
    {
        $logString = <<<'TEXT'
In the Project are:
  %8d files
  %8d top-level namespaces
  %8d unresolvable parent classes

TEXT;
        $logString = str_replace("\n", PHP_EOL, $logString);

        foreach ($this->descriptorCountByType as $class => $count) {
            $logString .= sprintf('  %8d %s elements' . PHP_EOL, $count, $class);
        }

        return sprintf(
            $logString,
            $this->fileCount,
            $this->topLevelNamespaceCount,
            $this->unresolvedParentClassesCount,
        );
    }

    /**
     * Increments the counter for element's class in the class counters.
     *
     * @param array<string, int> $classCounters
     * @phpstan-param array<class-string<ElementInterface>, int> $classCounters
     *
     * @return array<string, int>
     * @phpstan-return array<class-string<ElementInterface>, int>
     */
    protected function addElementToCounter(array $classCounters, ElementInterface $element): array
    {
        if (! isset($classCounters[$element::class])) {
            $classCounters[$element::class] = 0;
        }

        ++$classCounters[$element::class];

        return $classCounters;
    }

    /**
     * Checks whether the given element is a class and if its parent could not be resolved; increment the counter.
     */
    protected function incrementUnresolvedParentCounter(ElementInterface $element): void
    {
        if (! $element instanceof ClassInterface) {
            return;
        }

        if (! is_string($element->getParent())) {
            return;
        }

        ++$this->unresolvedParentClassesCount;
    }

    /**
     * Returns all elements from the project descriptor.
     *
     * @return Collection<ElementInterface>
     */
    protected function findAllElements(DocumentationSetDescriptor $documentationSet): Collection
    {
        return $documentationSet->getIndexes()->fetch(
            'elements',
            Collection::fromInterfaceString(ElementInterface::class),
        );
    }
}
