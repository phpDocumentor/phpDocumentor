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

trait CanBeFinal
{
    /** @var bool $final Whether this class is marked as final and can't be subclassed. */
    protected bool $final = false;

    /** @internal should not be called by any other class than the assemblers */
    public function setFinal(bool $final): void
    {
        $this->final = $final;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }
}
