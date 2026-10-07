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

namespace phpDocumentor\GuidesDirectives\Nodes;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Interfaces\AttributedInterface;
use phpDocumentor\Descriptor\Interfaces\AttributeInterface;
use phpDocumentor\Guides\Nodes\PHP\DescriptorNode;

/** @extends DescriptorNode<ClassDescriptor> */
final class DirectiveOptionsList extends DescriptorNode
{
    /** @return array<AttributeInterface> */
    public function getDirectiveOptions(): array
    {
        if ($this->descriptor instanceof AttributedInterface === false) {
            return [];
        }

        $result = [];
        foreach ($this->descriptor->getAttributes() as $attribute) {
            if ($attribute->getName() !== 'Option') {
                continue;
            }

            $result[] = $attribute;
        }

        return $result;
    }
}
