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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ChildInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\Tag;

use function method_exists;

trait HasMethods
{
    /** @var Collection<MethodInterface> $methods References to methods defined in this class. */
    protected Collection $methods;

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<MethodInterface> $methods
     */
    public function setMethods(Collection $methods): void
    {
        $this->methods = $methods;
    }

    /** @return Collection<MethodInterface> */
    public function getMethods(): Collection
    {
        if (! isset($this->methods)) {
            $this->methods = Collection::fromInterfaceString(MethodInterface::class);
        }

        return $this->methods;
    }

    /** @return Collection<MethodInterface> */
    public function getInheritedMethods(): Collection
    {
        $inheritedMethods = Collection::fromInterfaceString(MethodInterface::class);

        if (method_exists($this, 'getUsedTraits')) {
            foreach ($this->getUsedTraits() as $trait) {
                if (! $trait instanceof TraitInterface) {
                    continue;
                }

                $inheritedMethods = $inheritedMethods->merge($trait->getMethods());
            }
        }

        if ($this instanceof ChildInterface === false) {
            return $inheritedMethods;
        }

        $parent = $this->getParent();
        if ($parent instanceof self === false) {
            return $inheritedMethods;
        }

        $inheritedMethods = $inheritedMethods->merge(
            $parent->getMethods()->matches(
                static fn (MethodInterface $method) => (string) $method->getVisibility() !== 'private',
            ),
        );

        return $inheritedMethods->merge($parent->getInheritedMethods());
    }

    /** @return Collection<MethodInterface> */
    public function getMagicMethods(): Collection
    {
        $methodTags = $this->getTags()->fetch('method', new Collection())->filter(Tag\MethodDescriptor::class);

        $methods = Collection::fromInterfaceString(MethodInterface::class);

        foreach ($methodTags as $methodTag) {
            $method = new MethodDescriptor();
            $method->setName($methodTag->getMethodName());
            $method->setDescription($methodTag->getDescription());
            $method->setStatic($methodTag->isStatic());
            $method->setParent($this);
            $method->setReturnType($methodTag->getResponse()->getType());
            $method->setHasReturnByReference($methodTag->getHasReturnByReference());

            $returnTags = $method->getTags()->fetch('return', new Collection());
            $returnTags->add($methodTag->getResponse());

            foreach ($methodTag->getArguments() as $name => $argument) {
                $method->addArgument($name, $argument);
            }

            $methods->set($method->getName(), $method);
        }

        if (! $this instanceof ChildInterface) {
            return $methods;
        }

        $parent = $this->getParent();
        if ($parent instanceof static) {
            $methods = $methods->merge($parent->getMagicMethods());
        }

        return $methods;
    }
}
