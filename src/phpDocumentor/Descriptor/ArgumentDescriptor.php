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

use InvalidArgumentException;
use phpDocumentor\Descriptor\Interfaces\ArgumentInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;

/**
 * Descriptor representing a single Argument of a method or function.
 *
 * @api
 * @package phpDocumentor\AST
 */
class ArgumentDescriptor extends DescriptorAbstract implements Interfaces\ArgumentInterface
{
    use Traits\BelongsToMethod;
    use Traits\CanHaveAType;
    use Traits\CanHaveADefaultValue;

    /** @var bool $byReference whether the argument passes the parameter by reference instead of by value */
    protected bool $byReference = false;

    /** @var bool Determines if this Argument represents a variadic argument */
    protected bool $isVariadic = false;

    public function getInheritedElement(): ?ArgumentInterface
    {
        try {
            $method = $this->getMethod();
        } catch (InvalidArgumentException $e) {
            // TODO: Apparently, in our Mario's example this can be null. But that is weird. Investigate this after
            //       this PR
            return null;
        }

        $inheritedElement = $method->getInheritedElement();

        if ($inheritedElement instanceof MethodInterface) {
            $parents = $inheritedElement->getArguments();
            /** @var ArgumentInterface $parentArgument */
            foreach ($parents as $parentArgument) {
                if ($parentArgument->getName() === $this->getName()) {
                    return $parentArgument;
                }
            }
        }

        return null;
    }

    public function setByReference(bool $byReference): void
    {
        $this->byReference = $byReference;
    }

    public function isByReference(): bool
    {
        return $this->byReference;
    }

    /**
     * Sets whether this argument represents a variadic argument.
     */
    public function setVariadic(bool $isVariadic): void
    {
        $this->isVariadic = $isVariadic;
    }

    /**
     * Returns whether this argument represents a variadic argument.
     */
    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }
}
