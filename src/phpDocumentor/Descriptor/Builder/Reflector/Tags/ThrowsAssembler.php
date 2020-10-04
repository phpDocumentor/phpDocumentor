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

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\ThrowsDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

/**
 * Constructs a new descriptor from the Reflector for an `@throws` tag.
 *
 * This object will read the reflected information for the `@throws` tag and create a {@see ThrowsDescriptor} object
 * that can be used in the rest of the application and templates.
 *
 * @extends BaseTagAssembler<ThrowsDescriptor, Throws>
 */
class ThrowsAssembler extends BaseTagAssembler
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Throws $data
     */
    public function buildDescriptor(object $data) : ThrowsDescriptor
    {
        $descriptor = new ThrowsDescriptor($data->getName());
        $descriptor->setType(AssemblerAbstract::deduplicateTypes($data->getType()));

        return $descriptor;
    }
}
