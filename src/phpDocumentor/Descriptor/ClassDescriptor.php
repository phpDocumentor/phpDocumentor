<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

/**
 * Descriptor representing a Class.
 */
class ClassDescriptor extends DescriptorAbstract implements Interfaces\ClassInterface
{
    /** @var ClassDescriptor|null $extends Reference to an instance of the superclass for this class, if any. */
    protected $extends;

    /** @var Collection $implements References to interfaces that are implemented by this class. */
    protected $implements;

    /** @var boolean $abstract Whether this is an abstract class. */
    protected $abstract = false;

    /** @var boolean $final Whether this class is marked as final and can't be subclassed. */
    protected $final = false;

    /** @var Collection $constants References to constants defined in this class. */
    protected $constants;

    /** @var Collection $properties References to properties defined in this class. */
    protected $properties;

    /** @var Collection $methods References to methods defined in this class. */
    protected $methods;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setInterfaces(new Collection());
        $this->setConstants(new Collection());
        $this->setProperties(new Collection());
        $this->setMethods(new Collection());
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($extends)
    {
        $this->extends = $extends;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->extends;
    }

    /**
     * {@inheritDoc}
     */
    public function setInterfaces(Collection $implements)
    {
        $this->implements = $implements;
    }

    /**
     * {@inheritDoc}
     */
    public function getInterfaces()
    {
        return $this->implements;
    }

    /**
     * {@inheritDoc}
     */
    public function setFinal($final)
    {
        $this->final = $final;
    }

    /**
     * {@inheritDoc}
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * {@inheritDoc}
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * {@inheritDoc}
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * {@inheritDoc}
     */
    public function setConstants(Collection $constants)
    {
        $this->constants = $constants;
    }

    /**
     * {@inheritDoc}
     */
    public function getConstants($includeInherited = true)
    {
        if (!$includeInherited || !$this->getParent() || (!$this->getParent() instanceof ClassDescriptor)) {
            return $this->constants;
        }

        return $this->constants->merge($this->getParent()->getConstants(true));
    }

    /**
     * {@inheritDoc}
     */
    public function setMethods(Collection $methods)
    {
        $this->methods = $methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods($includeInherited = true)
    {
        if (!$includeInherited || !$this->getParent() || (!$this->getParent() instanceof ClassDescriptor)) {
            return $this->methods;
        }

        return $this->methods->merge($this->getParent()->getMethods(true));
    }

    /**
     * {@inheritDoc}
     */
    public function setProperties(Collection $properties)
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties($includeInherited = true)
    {
        if (!$includeInherited || !$this->getParent() || (!$this->getParent() instanceof ClassDescriptor)) {
            return $this->properties;
        }

        return $this->properties->merge($this->getParent()->getProperties(true));
    }

    public function setPackage($package)
    {
        parent::setPackage($package);

        foreach ($this->getConstants() as $constant) {
            $constant->setPackage($package);
        }

        foreach ($this->getProperties() as $property) {
            $property->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }
}
