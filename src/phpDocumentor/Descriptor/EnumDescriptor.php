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

use phpDocumentor\Descriptor\Interfaces\EnumCaseInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\Fqsen;

/**
 * Descriptor representing a Enum.
 *
 * @api
 * @package phpDocumentor\AST
 */
final class EnumDescriptor extends DescriptorAbstract implements EnumInterface
{
    /**
     * References to interfaces that are implemented by this enum.
     *
     * @var Collection<InterfaceDescriptor|Fqsen> $implements
     */
    private $implements;

    /** @var Collection<MethodDescriptor> $methods References to methods defined in this class. */
    private $methods;

    /** @var Collection<TraitDescriptor>|Collection<Fqsen> $usedTraits References to traits consumed by this class */
    private $usedTraits;

    /** @var Collection<EnumCaseInterface> */
    private $cases;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setInterfaces(new Collection());
        $this->setUsedTraits(new Collection());
        $this->setMethods(new Collection());
        $this->setCases(new Collection());
    }

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<InterfaceDescriptor|Fqsen> $implements
     */
    public function setInterfaces(Collection $implements): void
    {
        $this->implements = $implements;
    }

    public function getInterfaces(): Collection
    {
        return $this->implements;
    }

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<MethodDescriptor> $methods
     */
    public function setMethods(Collection $methods): void
    {
        $this->methods = $methods;
    }

    public function getMethods(): Collection
    {
        return $this->methods;
    }

    public function getInheritedMethods(): Collection
    {
        $inheritedMethods = Collection::fromClassString(MethodDescriptor::class);

        foreach ($this->getUsedTraits() as $trait) {
            if (!$trait instanceof TraitDescriptor) {
                continue;
            }

            $inheritedMethods = $inheritedMethods->merge($trait->getMethods());
        }

        return $inheritedMethods;
    }

    /**
     * @return Collection<MethodDescriptor>
     */
    public function getMagicMethods(): Collection
    {
        $methodTags = $this->getTags()->fetch('method', new Collection())->filter(Tag\MethodDescriptor::class);

        $methods = Collection::fromClassString(MethodDescriptor::class);

        foreach ($methodTags as $methodTag) {
            $method = new MethodDescriptor();
            $method->setName($methodTag->getMethodName());
            $method->setDescription($methodTag->getDescription());
            $method->setStatic($methodTag->isStatic());
            $method->setParent($this);

            $returnTags = $method->getTags()->fetch('return', new Collection())->filter(ReturnDescriptor::class);
            $returnTags->add($methodTag->getResponse());

            foreach ($methodTag->getArguments() as $name => $argument) {
                $method->addArgument($name, $argument);
            }

            $methods->add($method);
        }

        return $methods;
    }

    /**
     * @inheritDoc
     */
    public function setPackage($package): void
    {
        parent::setPackage($package);

        foreach ($this->getCases() as $case) {
            $case->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }

    public function setLocation(FileDescriptor $file, int $line = 0): void
    {
        parent::setLocation($file, $line);
        foreach ($this->getCases() as $case) {
            $case->setFile($file);
        }
    }

    /**
     * Sets a collection of all traits used by this class.
     *
     * @param Collection<TraitDescriptor>|Collection<Fqsen> $usedTraits
     */
    public function setUsedTraits(Collection $usedTraits): void
    {
        $this->usedTraits = $usedTraits;
    }

    /**
     * Returns the traits used by this class.
     *
     * Returned values may either be a string (when the Trait is not in this project) or a TraitDescriptor.
     *
     * @return Collection<TraitDescriptor>|Collection<Fqsen>
     */
    public function getUsedTraits(): Collection
    {
        return $this->usedTraits;
    }

    /**
     * @param Collection<EnumCaseInterface> $cases
     */
    public function setCases(Collection $cases): void
    {
        $this->cases = $cases;
    }

    /**
     * @return Collection<EnumCaseInterface>
     */
    public function getCases(): Collection
    {
        return $this->cases;
    }
}
