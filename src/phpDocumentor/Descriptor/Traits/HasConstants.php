<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ChildInterface;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;

use function method_exists;

trait HasConstants
{
    /** @var Collection<ConstantInterface> $constants References to constants defined in this class. */
    protected Collection $constants;

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<ConstantInterface> $constants
     */
    public function setConstants(Collection $constants): void
    {
        $this->constants = $constants;
    }

    /** @return Collection<ConstantInterface> */
    public function getConstants(): Collection
    {
        if (! isset($this->constants)) {
            $this->constants = Collection::fromInterfaceString(ConstantInterface::class);
        }

        return $this->constants;
    }

    /** @return Collection<ConstantInterface> */
    public function getInheritedConstants(): Collection
    {
        $inheritedConstants = Collection::fromInterfaceString(ConstantInterface::class);

        if (method_exists($this, 'getUsedTraits')) {
            foreach ($this->getUsedTraits() as $trait) {
                if (! $trait instanceof TraitInterface) {
                    continue;
                }

                $inheritedConstants = $inheritedConstants->merge($trait->getConstants());
            }
        }

        if ($this instanceof ChildInterface === false) {
            return $inheritedConstants;
        }

        $parent = $this->getParent();
        if (! $parent instanceof ClassInterface) {
            return $inheritedConstants;
        }

        $inheritedConstants = $inheritedConstants->merge(
            $parent->getConstants()->matches(
                static fn (ConstantInterface $constant) => (string) $constant->getVisibility() !== 'private',
            ),
        );

        return $inheritedConstants->merge($parent->getInheritedConstants());
    }
}
