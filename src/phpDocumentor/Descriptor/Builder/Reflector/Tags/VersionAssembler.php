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
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Version;

/**
 * Constructs a new descriptor from the Reflector for an `@version` tag.
 *
 * This object will read the reflected information for the `@version` tag and create a {@see VersionDescriptor} object
 * that can be used in the rest of the application and templates.
 */
class VersionAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Version $data
     */
    public function create(object $data) : VersionDescriptor
    {
        $descriptor = new VersionDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVersion($data->getVersion());

        return $descriptor;
    }
}
