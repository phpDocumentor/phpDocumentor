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

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;

/** @extends DescriptorNode<ElementInterface> */
final class ElementDescription extends DescriptorNode
{
    public function getDescription(): DescriptionDescriptor
    {
        return $this->descriptor?->getDescription() ?? DescriptionDescriptor::createEmpty();
    }
}
