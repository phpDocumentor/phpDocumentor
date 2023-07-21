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

trait CanBeAbstract
{
    /** @var bool $abstract Whether this is an abstract class. */
    protected bool $abstract = false;

    /** @internal should not be called by any other class than the assemblers */
    public function setAbstract(bool $abstract): void
    {
        $this->abstract = $abstract;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }
}
