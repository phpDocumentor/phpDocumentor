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

use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\See;

/**
 * Constructs a new Descriptor from a Reflector object for the `@see` tag.
 *
 * This class will gather the properties that were parsed by the Reflection mechanism for, specifically, an `@see` tag
 * and use that to create a SeeDescriptor that describes all properties that an `@see` tag may have.
 *
 * @extends BaseTagAssembler<SeeDescriptor, See>
 */
class SeeAssembler extends BaseTagAssembler
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param See $data
     */
    public function buildDescriptor(object $data): SeeDescriptor
    {
        $descriptor = new SeeDescriptor($data->getName());
        $reference = $data->getReference();
        $descriptor->setReference($reference);

        return $descriptor;
    }
}
