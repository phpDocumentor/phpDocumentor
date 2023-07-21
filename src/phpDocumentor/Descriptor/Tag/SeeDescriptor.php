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

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use Stringable;

/**
 * @api
 * @package phpDocumentor\AST\Tags
 */
class SeeDescriptor extends TagDescriptor implements Stringable
{
    private Reference $reference;

    public function __construct(string $name, Reference $reference, ?DescriptionDescriptor $description = null)
    {
        parent::__construct($name, $description);
        $this->reference = $reference;
    }

    public function getReference(): Reference
    {
        return $this->reference;
    }

    public function __toString(): string
    {
        return (string) $this->reference;
    }
}
