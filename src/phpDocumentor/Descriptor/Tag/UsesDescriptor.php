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

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Fqsen;

/**
 * Descriptor representing the uses tag on any element.
 *
 * @api
 * @package phpDocumentor\AST\Tags
 */
final class UsesDescriptor extends TagDescriptor
{
    /** @var Fqsen|Descriptor|null the FQSEN where the uses tag refers to */
    private $reference;

    /**
     * Returns the FQSEN, or Descriptor after linking, to which this tag points.
     *
     * @return Fqsen|Descriptor|null
     */
    public function getReference(): ?object
    {
        return $this->reference;
    }

    /**
     * Sets the FQSEN or Descriptor to which this tag points.
     *
     * @param Fqsen|Descriptor|null $reference
     */
    public function setReference(?object $reference): void
    {
        $this->reference = $reference;
    }
}
