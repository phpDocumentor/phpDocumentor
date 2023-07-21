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

namespace phpDocumentor\Descriptor\ValueObjects;

/** @template T as Equals */
interface Equals
{
    /** @param T $other */
    public function equals(Equals $other): bool;
}
