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
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

/**
 * Constructs a new descriptor from the Reflector for an `@author` tag.
 *
 * This object will read the reflected information for the `@author` tag and create a {@see AuthorDescriptor} object
 * that can be used in the rest of the application and templates.
 *
 * @extends AssemblerAbstract<AuthorDescriptor, Author>
 */
class AuthorAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Author $data
     */
    protected function buildDescriptor(object $data) : AuthorDescriptor
    {
        $tag = new AuthorDescriptor($data->getName());
        $tag->setDescription(new DescriptionDescriptor(new Description((string) $data), []));

        return $tag;
    }
}
