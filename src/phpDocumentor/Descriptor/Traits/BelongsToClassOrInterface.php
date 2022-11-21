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

use InvalidArgumentException;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Reflection\Fqsen;
use Webmozart\Assert\Assert;

trait BelongsToClassOrInterface
{
    /** @var ClassInterface|InterfaceInterface|null $parent */
    protected ?ElementInterface $parent = null;

    /**
     * Registers a parent class or interface with this constant.
     *
     * @param ClassInterface|InterfaceInterface|null $parent
     *
     * @throws InvalidArgumentException If anything other than a class, interface or null was passed.
     */
    public function setParent(?ElementInterface $parent): void
    {
        Assert::nullOrIsInstanceOfAny($parent, [ClassInterface::class, InterfaceInterface::class]);

        $fqsen = $parent !== null
            ? $parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName()
            : $this->getName();

        $this->setFullyQualifiedStructuralElementName(new Fqsen($fqsen));

        $this->parent = $parent;
    }

    /**
     * @return ClassInterface|InterfaceInterface|null
     */
    public function getParent(): ?ElementInterface
    {
        return $this->parent;
    }
}
