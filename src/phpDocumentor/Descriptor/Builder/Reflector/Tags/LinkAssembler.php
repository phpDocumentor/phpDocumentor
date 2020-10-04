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

use phpDocumentor\Descriptor\Tag\LinkDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Link;

/**
 * Constructs a new descriptor from the Reflector for an `@link` tag.
 *
 * This object will read the reflected information for the `@link` tag and create a {@see LinkDescriptor} object that
 * can be used in the rest of the application and templates.
 *
 * @extends BaseTagAssembler<LinkDescriptor, Link>
 */
class LinkAssembler extends BaseTagAssembler
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Link $data
     */
    public function buildDescriptor(object $data) : LinkDescriptor
    {
        $descriptor = new LinkDescriptor($data->getName());
        $descriptor->setLink($data->getLink());

        return $descriptor;
    }
}
