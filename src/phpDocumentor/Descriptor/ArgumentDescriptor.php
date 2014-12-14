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
namespace phpDocumentor\Descriptor;

/**
 * Descriptor representing a single Argument of a method or function.
 */
class ArgumentDescriptor extends DescriptorAbstract implements Interfaces\ArgumentInterface
{
    /** @var MethodDescriptor $method */
    protected $method;

    /** @var string[] $type an array of normalized types that should be in this Argument */
    protected $types = array();

    /** @var string|null $default the default value for an argument or null if none is provided */
    protected $default;

    /** @var bool $byReference whether the argument passes the parameter by reference instead of by value */
    protected $byReference = false;

    /** @var boolean Determines if this Argument represents a variadic argument */
    protected $isVariadic = false;

    /**
     * To which method does this argument belong to
     *
     * @param MethodDescriptor $method
     */
    public function setMethod(MethodDescriptor $method)
    {
        $this->method = $method;
    }

    /**
     * {@inheritDoc}
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        if (count($this->types)==0 &&
            $this->getInheritedElement() !== null
            ) {
                $this->setTypes($this->getInheritedElement()->getTypes());
        }

        return $this->types;
    }

    /**
     * @return null|ArgumentDescriptor
     */
    public function getInheritedElement()
    {
        if ($this->method instanceof MethodDescriptor &&
            $this->method->getInheritedElement() instanceof MethodDescriptor) {
            $parents = $this->method->getInheritedElement()->getArguments();
            foreach($parents as $parentArgument)
            {
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
