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

use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

/**
 * Constructs a new descriptor from the Reflector for an `@return` tag.
 *
 * This object will read the reflected information for the `@return` tag and create a {@see ReturnDescriptor} object
 * that can be used in the rest of the application and templates.
 *
 * @extends BaseTagAssembler<ReturnDescriptor, Return_>
 */
class ReturnAssembler extends BaseTagAssembler
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Return_ $data
     */
    public function buildDescriptor(object $data): ReturnDescriptor
    {
        $descriptor = new ReturnDescriptor($data->getName());
        $descriptor->setType($data->getType());

        return $descriptor;
    }
}
