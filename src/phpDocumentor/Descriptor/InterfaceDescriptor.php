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

use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Reflection\Fqsen;

/**
 * Descriptor representing an Interface.
 *
 * @api
 * @package phpDocumentor\AST
 */
class InterfaceDescriptor extends DescriptorAbstract implements Interfaces\InterfaceInterface
{
    use Traits\HasAttributes;

    /** @var Collection<InterfaceInterface|Fqsen> $parents */
    protected Collection $parents;

    /** @var Collection<ConstantInterface> $constants */
    protected Collection $constants;

    /** @var Collection<MethodInterface> $methods */
    protected Collection $methods;

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

    public function setParent(Collection $parents): void
    {
        $this->parents = $parents;
    }

    public function getParent(): Collection
    {
        return $this->parents;
    }

    public function setConstants(Collection $constants): void
    {
        $this->constants = $constants;
    }

    public function getConstants(): Collection
    {
        return $this->constants;
    }

    /** @return Collection<ConstantInterface> */
    public function getInheritedConstants(): Collection
    {
        $inheritedConstants = Collection::fromInterfaceString(ConstantInterface::class);

        /** @var InterfaceInterface|Fqsen $parent */
        foreach ($this->getParent() as $parent) {
            if (! $parent instanceof Interfaces\InterfaceInterface) {
                continue;
            }

            $inheritedConstants = $inheritedConstants->merge($parent->getConstants());
            $inheritedConstants = $inheritedConstants->merge($parent->getInheritedConstants());
        }

        return $inheritedConstants;
    }

    public function setMethods(Collection $methods): void
    {
        $this->methods = $methods;
    }

    public function getMethods(): Collection
    {
        return $this->methods;
    }

    /** @return Collection<MethodInterface> */
    public function getInheritedMethods(): Collection
    {
        $inheritedMethods = Collection::fromInterfaceString(MethodInterface::class);

        /** @var InterfaceInterface|Fqsen $parent */
        foreach ($this->getParent() as $parent) {
            if ($parent instanceof Fqsen) {
                continue;
            }

            $inheritedMethods = $inheritedMethods->merge($parent->getMethods());
            $inheritedMethods = $inheritedMethods->merge($parent->getInheritedMethods());
        }

        return $inheritedMethods;
    }

    /** @inheritDoc */
    public function setPackage($package): void
    {
        parent::setPackage($package);

        foreach ($this->getConstants() as $constant) {
            $constant->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }

    /** @return InterfaceInterface|Fqsen|null */
    public function getInheritedElement(): object|null
    {
        return $this->getParent()->count() > 0
            ? $this->getParent()->getIterator()->current()
            : null;
    }
}
