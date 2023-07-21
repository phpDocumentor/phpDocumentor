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

use phpDocumentor\Descriptor\ValueObjects\IsApplicable;

trait CanBeVariadic
{
    /** @var IsApplicable Determines if this Argument represents a variadic argument */
    protected IsApplicable $isVariadic;

    /**
     * Sets whether this argument represents a variadic argument.
     */
    public function setVariadic(IsApplicable $isVariadic): void
    {
        $this->isVariadic = $isVariadic;
    }

    /**
     * Returns whether this argument represents a variadic argument.
     */
    public function isVariadic(): bool
    {
        if (! isset($this->isVariadic)) {
            $this->isVariadic = IsApplicable::false();
        }

        return $this->isVariadic->equals(IsApplicable::true());
    }
}
