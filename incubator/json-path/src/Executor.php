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
use phpDocumentor\JsonPath\AST\Expression;
use phpDocumentor\JsonPath\AST\FieldName;
use phpDocumentor\JsonPath\AST\PathNode;
use phpDocumentor\JsonPath\AST\QueryNode;
use phpDocumentor\JsonPath\AST\Wildcard;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

use function array_merge;
use function current;
use function is_array;
use function is_iterable;
use function is_object;
use function is_string;
use function iterator_to_array;
use function str_starts_with;
use function strrpos;
use function substr;

final class Executor
{
    private readonly PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /** @return mixed */
    public function evaluate(QueryNode $query, mixed $currentElement, mixed $rootElement = null)
    {
        return $query->visit($this, $currentElement, $rootElement);
    }

    public function evaluateEqualsComparison(
        mixed $root,
        mixed $currentObject,
        QueryNode $left,
        QueryNode $right,
    ): bool {
        $leftValue = $this->toValue($this->evaluate($left, $currentObject, $root));
        $rightValue = $this->toValue($this->evaluate($right, $currentObject, $root));

        if (is_string($rightValue)) {
            return ((string) $leftValue) === $rightValue;
        }

        return $leftValue === $rightValue;
    }

    public function evaluateNotEqualsComparison(
        mixed $root,
        mixed $currentObject,
        QueryNode $left,
        QueryNode $right,
    ): bool {
        return ! $this->evaluateEqualsComparison($root, $currentObject, $left, $right);
    }

    public function evaluateStartsWithComparison(
        mixed $root,
        mixed $currentObject,
        QueryNode $left,
        QueryNode $right,
    ): bool {
        $leftValue = $this->toValue($this->evaluate($left, $currentObject, $root));
        $rightValue = $this->toValue($this->evaluate($right, $currentObject, $root));

        return str_starts_with((string) $leftValue, (string) $rightValue);
    }

    public function evaluateContainsComparison(
        mixed $root,
        mixed $currentObject,
        QueryNode $left,
        QueryNode $right,
    ): bool {
        $leftValue = $this->toValue($this->evaluate($left, $currentObject, $root));
        $rightValue = $this->toValue($this->evaluate($right, $currentObject, $root));

        if (is_iterable($leftValue)) {
            foreach ($leftValue as $value) {
                if (is_string($rightValue) && ((string) $value) === $rightValue) {
                    return true;
                }

                if ($value === $rightValue) {
                    return true;
                }
            }

            return false;
        }

        return false;
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

    /** @return mixed */
    public function evaluatePath(mixed $root, mixed $currentElement, PathNode ...$nodes)
    {
        $result = $currentElement;
        foreach ($nodes as $node) {
            $result = $this->evaluate($node, $result, $root);
        }

        return $result;
    }

    /** @return mixed */
    public function evaluateFunctionCall(
        mixed $root,
        mixed $currentElement,
        string $functionName,
        QueryNode ...$arguments,
    ) {
        switch ($functionName) {
            case 'type':
                $class = $this->evaluate($arguments[0], $currentElement, $root)::class;
                $isNamespacedClass = strrpos($class, '\\');
                if ($isNamespacedClass !== false) {
                    return substr($class, $isNamespacedClass + 1);
                }

                return $class;
        }
    }

    /** @return Generator<mixed> */
    public function evaluateFieldAccess(mixed $currentElement, FieldName|Expression $fieldName): Generator
    {
        if ($fieldName instanceof Wildcard && is_iterable($currentElement)) {
            foreach ($currentElement as $element) {
                foreach ($element as $value) {
                    yield $value;
                }
            }

            return;
        }

        if ($currentElement instanceof Generator) {
            foreach ($currentElement as $element) {
                foreach ($this->evaluateFieldAccess($element, $fieldName) as $result) {
                    yield $result;
                }
            }

            return;
        }

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
                            is_array($row) ? $row : iterator_to_array($row, false),
                        );
                        continue;
                    }

                    $result[] = $row;
                }
            }

            yield from $result;
        } else {
            if (is_object($currentElement) === false) {
                return;
            }

            if (
                $this->propertyAccessor->isReadable(
                    $currentElement,
                    new PropertyPath($fieldName->getName()),
                ) === false
            ) {
                return;
            }

            yield $this->propertyAccessor->getValue($currentElement, new PropertyPath($fieldName->getName()));
        }
    }
}
