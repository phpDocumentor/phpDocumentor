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

trait CanBeByReference
{
    /** @var IsApplicable $byReference whether the argument passes the parameter by reference instead of by value */
    protected IsApplicable $byReference;

    public function setByReference(IsApplicable $byReference): void
    {
        $this->byReference = $byReference;
    }

    public function isByReference(): bool
    {
        if (! isset($this->byReference)) {
            $this->byReference = IsApplicable::false();
        }

        return $this->byReference->equals(IsApplicable::true());
    }
}
