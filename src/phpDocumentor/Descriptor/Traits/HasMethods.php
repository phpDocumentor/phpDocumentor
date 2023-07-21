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
use phpDocumentor\Descriptor\Interfaces\MethodInterface;

trait HasMethods
{
    /** @var Collection<MethodInterface> $methods References to methods defined in this class. */
    protected Collection $methods;

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<MethodInterface> $methods
     */
    public function setMethods(Collection $methods): void
    {
        $this->methods = $methods;
    }

    /** @return Collection<MethodInterface> */
    public function getMethods(): Collection
    {
        if (! isset($this->methods)) {
            $this->methods = Collection::fromInterfaceString(MethodInterface::class);
        }

        return $this->methods;
    }
}
