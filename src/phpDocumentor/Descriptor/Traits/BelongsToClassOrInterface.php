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
     * @throws InvalidArgumentException If anything other than a class or interface was passed.
     *
     * @inheritDoc
     */
    public function setParent($parent): void
    {
        Assert::isInstanceOfAny($parent, [ClassInterface::class, InterfaceInterface::class]);

        $this->setFullyQualifiedStructuralElementName(
            new Fqsen($parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName())
        );

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
