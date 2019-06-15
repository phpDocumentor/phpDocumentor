<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\Type;

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

    /** @var boolean Determines if this Argument represents a variadic argument */
    protected $isVariadic = false;

    /**
     * To which method does this argument belong to
     */
    public function setMethod(MethodDescriptor $method)
    {
        $this->method = $method;
    }

    public function getMethod(): ?MethodDescriptor
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function setType(?Type $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): ?Type
    {
        if ($this->type === null && $this->getInheritedElement() !== null) {
            $this->setType($this->getInheritedElement()->getType());
        }

        return $this->type;
    }

    public function getTypes(): array
    {
        trigger_error('Please use getType', E_USER_DEPRECATED);
        return [$this->getType()];
    }

    /**
     * @return null|ArgumentDescriptor
     */
    public function getInheritedElement()
    {
        if ($this->method instanceof MethodDescriptor &&
            $this->method->getInheritedElement() instanceof MethodDescriptor) {
            $parents = $this->method->getInheritedElement()->getArguments();
            foreach ($parents as $parentArgument) {
                if ($parentArgument->getName() === $this->getName()) {
                    return $parentArgument;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault($value)
    {
        $this->default = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * {@inheritDoc}
     */
    public function setByReference($byReference)
    {
        $this->byReference = $byReference;
    }

    /**
     * {@inheritDoc}
     */
    public function isByReference()
    {
        return $this->byReference;
    }

    /**
     * Sets whether this argument represents a variadic argument.
     *
     * @param boolean $isVariadic
     *
     * @return false
     */
    public function setVariadic($isVariadic)
    {
        $this->isVariadic = $isVariadic;
    }

    /**
     * Returns whether this argument represents a variadic argument.
     *
     * @return boolean
     */
    public function isVariadic()
    {
        return $this->isVariadic;
    }
}
