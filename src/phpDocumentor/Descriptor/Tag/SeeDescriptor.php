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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;

/**
 * @api
 * @package phpDocumentor\AST\Tags
 */
class SeeDescriptor extends TagDescriptor
{
    /** @var Reference $reference */
    private $reference;

    public function setReference(Reference $reference) : void
    {
        $this->reference = $reference;
    }

    public function getReference() : Reference
    {
        return $this->reference;
    }
}
