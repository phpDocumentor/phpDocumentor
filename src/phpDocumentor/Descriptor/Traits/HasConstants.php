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
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;

use function method_exists;

trait HasConstants
{
    /** @var CollectionInterface<ConstantInterface> $constants References to constants defined in this class. */
    protected CollectionInterface $constants;

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param CollectionInterface<ConstantInterface> $constants
     */
    public function setConstants(CollectionInterface $constants): void
    {
        $this->constants = $constants;
    }

    /** @return CollectionInterface<ConstantInterface> */
    public function getConstants(): CollectionInterface
    {
        if (! isset($this->constants)) {
            $this->constants = Collection::fromInterfaceString(ConstantInterface::class);
        }

        return $this->constants;
    }

    /** @return CollectionInterface<ConstantInterface> */
    public function getInheritedConstants(): CollectionInterface
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
