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
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Reflection\Fqsen;

trait UsesTraits
{
    /** @var Collection<TraitInterface|Fqsen> $usedTraits References to traits consumed by this class */
    protected Collection $usedTraits;

    /**
     * Sets a collection of all traits used by this class.
     *
     * @param Collection<TraitInterface|Fqsen> $usedTraits
     */
    public function setUsedTraits(Collection $usedTraits): void
    {
        $this->usedTraits = $usedTraits;
    }

    /**
     * Returns the traits used by this class.
     *
     * Returned values may either be a string (when the Trait is not in this project) or a TraitDescriptor.
     *
     * @return Collection<TraitInterface|Fqsen>
     */
    public function getUsedTraits(): Collection
    {
        if (! isset($this->usedTraits)) {
            $this->usedTraits = new Collection();
        }

        return $this->usedTraits;
    }
}
