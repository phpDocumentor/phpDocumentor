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
 * Descriptor representing an Interface.
 */
class InterfaceDescriptor extends DescriptorAbstract implements Interfaces\InterfaceInterface
{
    /** @var Collection $extends */
    protected $parents;

    /** @var Collection $constants */
    protected $constants;

    /** @var Collection $methods */
    protected $methods;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setParent(new Collection());
        $this->setConstants(new Collection());
        $this->setMethods(new Collection());
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($parents)
    {
        $this->parents = $parents;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->parents;
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
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * {@inheritDoc}
     */
    public function getInheritedConstants()
    {
        if (!$this->getParent() || !$this->getParent() instanceof Collection || $this->getParent()->count() === 0) {
            return new Collection();
        }

        $inheritedConstants = new Collection();

        /** @var self $parent */
        foreach ($this->getParent() as $parent) {
            if (!$parent instanceof Interfaces\InterfaceInterface) {
                continue;
            }

            $inheritedConstants = $inheritedConstants->merge($parent->getConstants());
            $inheritedConstants = $inheritedConstants->merge($parent->getInheritedConstants());
        }

        return $inheritedConstants;
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
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getInheritedMethods()
    {
        if (!$this->getParent() || !$this->getParent() instanceof Collection || $this->getParent()->count() === 0) {
            return new Collection();
        }

        $inheritedMethods = new Collection();

        /** @var self $parent */
        foreach ($this->getParent() as $parent) {
            if (!$parent instanceof Interfaces\InterfaceInterface) {
                continue;
            }

            $inheritedMethods = $inheritedMethods->merge($parent->getMethods());
            $inheritedMethods = $inheritedMethods->merge($parent->getInheritedMethods());
        }

        return $inheritedMethods;
    }

    public function setPackage($package)
    {
        parent::setPackage($package);

        foreach ($this->getConstants() as $constant) {
            $constant->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }

    public function getInheritedElement()
    {
        return $this->getParent() && $this->getParent()->count() > 0
            ? $this->getParent()->getIterator()->current()
            : null;
    }
}
