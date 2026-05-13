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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Reflection\Fqsen;

trait UsesTraits
{
    /** @var CollectionInterface<TraitInterface|Fqsen> $usedTraits References to traits consumed by this class */
    protected CollectionInterface $usedTraits;

    /**
     * Sets a collection of all traits used by this class.
     *
     * @param CollectionInterface<TraitInterface|Fqsen> $usedTraits
     */
    public function setUsedTraits(CollectionInterface $usedTraits): void
    {
        $this->usedTraits = $usedTraits;
    }

    /**
     * Returns the traits used by this class.
     *
     * Returned values may either be a string (when the Trait is not in this project) or a TraitDescriptor.
     *
     * @return CollectionInterface<TraitInterface|Fqsen>
     */
    public function getUsedTraits(): CollectionInterface
    {
        if (! isset($this->usedTraits)) {
            $this->usedTraits = new Collection();
        }

        return $this->usedTraits;
    }
}
