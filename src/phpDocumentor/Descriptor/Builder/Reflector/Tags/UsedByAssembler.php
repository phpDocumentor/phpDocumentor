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

use phpDocumentor\Descriptor\Tag\UsedByDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;

/** @extends BaseTagAssembler<UsedByDescriptor, Uses> */
class UsedByAssembler extends BaseTagAssembler
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Uses $data
     */
    public function buildDescriptor(object $data): UsedByDescriptor
    {
        $descriptor = new UsedByDescriptor($data->getName());
        $reference = $data->getReference();

        $descriptor->setReference($reference);

        return $descriptor;
    }
}
