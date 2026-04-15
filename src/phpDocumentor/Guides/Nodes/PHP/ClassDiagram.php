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

use phpDocumentor\Descriptor\Descriptor;

/** @extends DescriptorNode<Descriptor> */
final class ClassDiagram extends DescriptorNode
{
    private string $caption = '';

    public function __construct(string $query)
    {
        parent::__construct("$.documentationSets.*[?(type(@) == 'ApiSetDescriptor')].indexes.classes.*" . $query);
    }

    public function setCaption(string|null $caption): self
    {
        $that = clone $this;
        $that->caption = $caption ?? '';

        return $that;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
