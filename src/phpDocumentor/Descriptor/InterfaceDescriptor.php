<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\Fqsen;

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

    public function setParent(Collection $parents) : void
    {
        $this->parents = $parents;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent() : Collection
    {
        return $this->parents;
    }

    /**
     * {@inheritDoc}
     */
    public function setConstants(Collection $constants) : void
    {
        $this->constants = $constants;
    }

    /**
     * {@inheritDoc}
     */
    public function getConstants() : Collection
    {
        return $this->constants;
    }

    /**
     * {@inheritDoc}
     */
    public function getInheritedConstants() : Collection
    {
        if ($this->getParent() === null
            || !$this->getParent() instanceof Collection
            || $this->getParent()->count() === 0
        ) {
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
    public function setMethods(Collection $methods) : void
    {
        $this->methods = $methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods() : Collection
    {
        return $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getInheritedMethods() : Collection
    {
        if ($this->getParent() === null
            || !$this->getParent() instanceof Collection
            || $this->getParent()->count() === 0
        ) {
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

    /**
     * @inheritDoc
     */
    public function setPackage($package) : void
    {
        parent::setPackage($package);

        foreach ($this->getConstants() as $constant) {
            $constant->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }

    /**
     * @return InterfaceDescriptor|Fqsen|null
     */
    public function getInheritedElement()
    {
        return $this->getParent()->count() > 0
            ? $this->getParent()->getIterator()->current()
            : null;
    }
}
