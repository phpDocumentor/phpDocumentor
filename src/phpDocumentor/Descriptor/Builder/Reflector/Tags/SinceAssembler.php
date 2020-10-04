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

use phpDocumentor\Descriptor\Tag\SinceDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Since;

/**
 * Constructs a new descriptor from the Reflector for an `@since` tag.
 *
 * This object will read the reflected information for the `@since` tag and create a {@see SinceDescriptor} object that
 * can be used in the rest of the application and templates.
 *
 * @extends BaseTagAssembler<SinceDescriptor, Since>
 */
class SinceAssembler extends BaseTagAssembler
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Since $data
     */
    public function buildDescriptor(object $data) : SinceDescriptor
    {
        $descriptor = new SinceDescriptor($data->getName());
        $descriptor->setVersion($data->getVersion());

        return $descriptor;
    }
}
