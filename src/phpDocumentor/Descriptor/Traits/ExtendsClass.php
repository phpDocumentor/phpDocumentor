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

use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Reflection\Fqsen;
use Webmozart\Assert\Assert;

trait ExtendsClass
{
    /**
     * Reference to an instance of the superclass for this class, if any.
     *
     * @var ClassInterface|Fqsen|string|null $parent
     */
    protected $parent;

    /**
     * @internal should not be called by any other class than the assemblers.
     *
     * @param ElementInterface|Fqsen|string $parent
     *
     * @psalm-ignore MoreSpecificImplementedParamType
     */
    public function setParent($parent): void
    {
        if ($parent instanceof ElementInterface) {
            Assert::isInstanceOf($parent, ClassInterface::class);
        }

        $this->parent = $parent;
    }

    /** @return ClassInterface|Fqsen|string|null */
    public function getParent()
    {
        return $this->parent;
    }
}
