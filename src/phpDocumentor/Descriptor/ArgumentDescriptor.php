<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\Type;
use function trigger_error;
use const E_USER_DEPRECATED;

/**
 * Descriptor representing a single Argument of a method or function.
 */
class ArgumentDescriptor extends DescriptorAbstract implements Interfaces\ArgumentInterface
{
    /** @var MethodDescriptor $method */
    protected $method;

    /** @var Type|null $type normalized type of this argument */
    protected $type = null;

    /** @var string|null $default the default value for an argument or null if none is provided */
    protected $default;

    /** @var bool $byReference whether the argument passes the parameter by reference instead of by value */
    protected $byReference = false;

    /** @var bool Determines if this Argument represents a variadic argument */
    protected $isVariadic = false;

    /**
     * To which method does this argument belong to
     */
    public function setMethod(MethodDescriptor $method) : void
    {
        $this->method = $method;
    }

    public function getMethod() : ?MethodDescriptor
    {
        return $this->method;
    }

    public function setType(?Type $type) : void
    {
        $this->type = $type;
    }

    public function getType() : ?Type
    {
        if ($this->type === null && $this->getInheritedElement() !== null) {
            $this->setType($this->getInheritedElement()->getType());
        }

        return $this->type;
    }

    public function getInheritedElement() : ?ArgumentDescriptor
    {
        if ($this->method instanceof MethodDescriptor &&
            $this->method->getInheritedElement() instanceof MethodDescriptor) {
            $parents = $this->method->getInheritedElement()->getArguments();
            /** @var ArgumentDescriptor $parentArgument */
            foreach ($parents as $parentArgument) {
                if ($parentArgument->getName() === $this->getName()) {
                    return $parentArgument;
                }
            }
        }

        return null;
    }

    public function setDefault(?string $value) : void
    {
        $this->default = $value;
    }

    public function getDefault() : ?string
    {
        return $this->default;
    }

    /**
     * {@inheritDoc}
     */
    public function setByReference($byReference) : void
    {
        $this->byReference = $byReference;
    }

    public function isByReference() : bool
    {
        return $this->byReference;
    }

    /**
     * Sets whether this argument represents a variadic argument.
     */
    public function setVariadic(bool $isVariadic) : void
    {
        $this->isVariadic = $isVariadic;
    }

    /**
     * Returns whether this argument represents a variadic argument.
     */
    public function isVariadic() : bool
    {
        return $this->isVariadic;
    }
}
