<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Fqsen;

/**
 * Descriptor representing the uses tag on any element.
 */
class UsesDescriptor extends TagDescriptor
{
    /** @var Fqsen the FQSEN where the uses tag refers to */
    protected $reference;

    /**
     * Returns the FQSEN to which this tag points.
     */
    public function getReference() : ?Fqsen
    {
        return $this->reference;
    }

    /**
     * Sets the FQSEN to which this tag points.
     */
    public function setReference(Fqsen $reference) : void
    {
        $this->reference = $reference;
    }
}
