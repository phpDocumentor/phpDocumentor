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
use phpDocumentor\Descriptor\Tag\PropertyDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Property;

/**
 * Constructs a new descriptor from the Reflector for an `@property` tag.
 *
 * This object will read the reflected information for the `@property` tag and create a {@see PropertyDescriptor}
 * object that can be used in the rest of the application and templates.
 */
class PropertyAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Property $data
     */
    public function create(object $data) : PropertyDescriptor
    {
        $descriptor = new PropertyDescriptor($data->getName());
        $descriptor->setVariableName($data->getVariableName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setType($data->getType());

        return $descriptor;
    }
}
