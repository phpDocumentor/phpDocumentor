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
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Type;

/**
 * Descriptor representing a Enum.
 *
 * @api
 * @package phpDocumentor\AST
 */
final class EnumDescriptor extends DescriptorAbstract implements EnumInterface
{
    use Traits\HasAttributes;
    use Traits\ImplementsInterfaces;
    use Traits\HasConstants;
    use Traits\HasMethods;
    use Traits\UsesTraits;

    /** @var Collection<EnumCaseInterface> */
    private Collection $cases;

    private Type|null $backedType = null;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setCases(new Collection());
    }

    /** @return Collection<MethodInterface> */
    public function getInheritedMethods(): Collection
    {
        $inheritedMethods = Collection::fromInterfaceString(MethodInterface::class);

        foreach ($this->getUsedTraits() as $trait) {
            if (! $trait instanceof TraitInterface) {
                continue;
            }

            $inheritedMethods = $inheritedMethods->merge($trait->getMethods());
        }

        return $inheritedMethods;
    }

    /** @inheritDoc */
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

    public function setLocation(FileInterface $file, Location $startLocation): void
    {
        parent::setLocation($file, $startLocation);

        foreach ($this->getCases() as $case) {
            $case->setFile($file);
        }
    }

    /** @param Collection<EnumCaseInterface> $cases */
    public function setCases(Collection $cases): void
    {
        $this->cases = $cases;
    }

    /** @return Collection<EnumCaseInterface> */
    public function getCases(): Collection
    {
        return $this->cases;
    }

    public function setBackedType(Type|null $type): void
    {
        $this->backedType = $type;
    }

    public function getBackedType(): Type|null
    {
        return $this->backedType;
    }
}
