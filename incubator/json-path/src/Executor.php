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

namespace phpDocumentor\JsonPath;

use ArrayAccess;
use Generator;
use phpDocumentor\JsonPath\AST\FieldName;
use phpDocumentor\JsonPath\AST\PathNode;
use phpDocumentor\JsonPath\AST\QueryNode;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

use function array_merge;
use function current;
use function get_class;
use function is_array;
use function is_iterable;
use function iterator_to_array;
use function strrpos;
use function substr;

final class Executor
{
    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param mixed $currentElement
     * @param mixed $rootElement
     *
     * @return mixed
     */
    public function evaluate(QueryNode $query, $currentElement, $rootElement = null)
    {
        return $query->visit($this, $currentElement, $rootElement);
    }

    /**
     * @param mixed $root
     * @param mixed $currentObject
     */
    public function evaluateEqualsComparison($root, $currentObject, QueryNode $left, QueryNode $right): bool
    {
        $leftValue = $this->toValue($this->evaluate($left, $currentObject, $root));
        $rightValue = $this->toValue($this->evaluate($right, $currentObject, $root));

        return $leftValue === $rightValue;
    }

    /**
     * @param Generator<mixed>|mixed $value
     *
     * @return mixed
     */
    private function toValue($value)
    {
        if ($value instanceof Generator) {
            $result = iterator_to_array($value, false);

            return current($result);
        }

        return $value;
    }

    /**
     * @param mixed $root
     * @param mixed $currentElement
     *
     * @return mixed
     */
    public function evaluatePath($root, $currentElement, PathNode ...$nodes)
    {
        $result = $currentElement;
        foreach ($nodes as $node) {
            $result = $this->evaluate($node, $result, $root);
        }

        return $result;
    }

    /**
     * @param mixed $root
     * @param mixed $currentElement
     *
     * @return mixed
     */
    public function evaluateFunctionCall($root, $currentElement, string $functionName, QueryNode ...$arguments)
    {
        switch ($functionName) {
            case 'type':
                $class = get_class($this->evaluate($arguments[0], $currentElement, $root));
                $isNamespacedClass = strrpos($class, '\\');
                if ($isNamespacedClass !== false) {
                    return substr($class, $isNamespacedClass + 1);
                }

                return $class;
        }
    }

    /**
     * @param mixed $currentElement
     *
     * @return Generator<mixed>
     */
    public function evaluateFieldAccess($currentElement, FieldName $fieldName): Generator
    {
        if (
            (is_array($currentElement) || $currentElement instanceof ArrayAccess) &&
            isset($currentElement[$fieldName->getName()])
        ) {
            yield $currentElement[$fieldName->getName()];
        } elseif (is_iterable($currentElement)) {
            $result = [];
            foreach ($currentElement as $element) {
                foreach ($this->evaluateFieldAccess($element, $fieldName) as $row) {
                    if (is_iterable($row)) {
                        $result = array_merge(
                            $result,
                            is_array($row) ? $row : iterator_to_array($row, false)
                        );
                        continue;
                    }

                    $result[] = $row;
                }
            }

            yield from $result;
        } else {
            yield $this->propertyAccessor->getValue($currentElement, new PropertyPath($fieldName->getName()));
        }
    }
}
