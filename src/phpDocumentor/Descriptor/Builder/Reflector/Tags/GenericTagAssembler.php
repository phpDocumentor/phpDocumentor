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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
 * @extends BaseTagAssembler<TagDescriptor, Tag>
 */
class GenericTagAssembler extends BaseTagAssembler
{
    /**
     * @param Tag $data
     */
    protected function buildDescriptor(object $data) : TagDescriptor
    {
        return new TagDescriptor($data->getName());
    }
}
