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

use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;

use function array_filter;

/**
 * Descriptor representing a constant
 *
 * @api
 * @package phpDocumentor\AST
 */
class ConstantDescriptor extends DescriptorAbstract implements
    Interfaces\ConstantInterface,
    Interfaces\VisibilityInterface
{
    use Traits\CanBeFinal;
    use Traits\HasVisibility;
    use Traits\BelongsToClassOrInterface;

    protected ?Type $types = null;
    protected string $value = '';

    public function setTypes(Type $types): void
    {
        $this->types = $types;
    }

    /**
     * @return list<Type>
     */
    public function getTypes(): array
    {
        return array_filter([$this->getType()]);
    }

    public function getType(): ?Type
    {
        if ($this->types === null) {
            $var = $this->getVar()->fetch(0);
            if ($var instanceof VarDescriptor) {
                return $var->getType();
            }
        }

        return $this->types;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return Collection<VarDescriptor>
     */
    public function getVar(): Collection
    {
        /** @var Collection<VarDescriptor> $var */
        $var = $this->getTags()->fetch('var', new Collection());
        if ($var->count() !== 0) {
            return $var;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getVar();
        }

        return new Collection();
    }

    /**
     * Returns the file associated with the parent class, interface or trait when inside a container.
     */
    public function getFile(): FileDescriptor
    {
        $file = parent::getFile() ?? $this->getParent()->getFile();

        Assert::notNull($file);

        return $file;
    }

    /**
     * Returns the Constant from which this one should inherit, if any.
     */
    public function getInheritedElement(): ?ConstantDescriptor
    {
        /** @var ClassDescriptor|InterfaceDescriptor|null $associatedClass */
        $associatedClass = $this->getParent();

        if (
            ($associatedClass instanceof ClassDescriptor || $associatedClass instanceof InterfaceDescriptor)
            && ($associatedClass->getParent() instanceof ClassDescriptor
                || $associatedClass->getParent() instanceof InterfaceDescriptor
            )
        ) {
            /** @var ClassDescriptor|InterfaceDescriptor $parentClass */
            $parentClass = $associatedClass->getParent();

            return $parentClass->getConstants()->fetch($this->getName());
        }

        return null;
    }
}
