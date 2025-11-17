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

use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Reflection\Php\Expression;
use Webmozart\Assert\Assert;

/**
 * Descriptor representing a property.
 *
 * @api
 * @package phpDocumentor\AST
 */
class EnumCaseDescriptor extends DescriptorAbstract implements Interfaces\EnumCaseInterface
{
    use Traits\HasAttributes;

    private EnumInterface|null $parent = null;

    private Expression|null $value = null;

    public function setValue(Expression|null $value): void
    {
        $this->value = $value;
    }

    public function getValue(): Expression|null
    {
        return $this->value;
    }

    public function getParent(): EnumInterface|null
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($parent): void
    {
        Assert::nullOrIsInstanceOf($parent, EnumInterface::class);

        $this->parent = $parent;
    }
}
