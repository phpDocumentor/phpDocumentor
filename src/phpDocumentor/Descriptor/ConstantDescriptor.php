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

use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;

/**
 * Descriptor representing a constant
 *
 * @api
 * @package phpDocumentor\AST
 */
class ConstantDescriptor extends DescriptorAbstract implements
    Interfaces\ConstantInterface,
    Interfaces\VisibilityInterface,
    Interfaces\AttributedInterface
{
    use Traits\CanBeFinal;
    use Traits\HasVisibility;
    use Traits\BelongsToClassTraitOrInterface;
    use Traits\CanHaveAType;
    use Traits\HasAttributes;

    protected Expression $value;

    public function __construct()
    {
        parent::__construct();

        $this->value = new Expression('?');
    }

    public function getType(): Type|null
    {
        if ($this->type === null) {
            $var = $this->getVar()->fetch(0);
            if ($var instanceof VarDescriptor) {
                return $var->getType();
            }
        }

        return $this->type;
    }

    public function setValue(Expression $value): void
    {
        $this->value = $value;
    }

    public function getValue(): Expression
    {
        return $this->value;
    }

    /** @return Collection<VarDescriptor> */
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
    public function getFile(): FileInterface
    {
        $file = parent::getFile() ?? $this->getParent()->getFile();

        Assert::notNull($file);

        return $file;
    }

    /**
     * Returns the Constant from which this one should inherit, if any.
     */
    public function getInheritedElement(): ConstantInterface|null
    {
        /** @var ClassInterface|InterfaceInterface|null $associatedClass */
        $associatedClass = $this->getParent();

        if (
            ($associatedClass instanceof ClassInterface || $associatedClass instanceof InterfaceInterface)
            && ($associatedClass->getParent() instanceof ClassInterface
                || $associatedClass->getParent() instanceof InterfaceInterface
            )
        ) {
            /** @var ClassInterface|InterfaceInterface $parentClass */
            $parentClass = $associatedClass->getParent();

            return $parentClass->getConstants()->fetch($this->getName());
        }

        return null;
    }
}
