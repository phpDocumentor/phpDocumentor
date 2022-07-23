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

/**
 * Descriptor representing a property.
 *
 * @api
 * @package phpDocumentor\AST
 */
class EnumCaseDescriptor extends DescriptorAbstract implements Interfaces\EnumCaseInterface
{
    private ?EnumDescriptor $parent = null;

    private ?string $value;

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getParent(): ?EnumDescriptor
    {
        return $this->parent;
    }

    public function setParent(?EnumDescriptor $parent): void
    {
        $this->parent = $parent;
    }
}
