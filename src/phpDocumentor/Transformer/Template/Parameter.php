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

namespace phpDocumentor\Transformer\Template;

use Stringable;

/**
 * Model representing a parameter in a template or transformation.
 */
final class Parameter implements Stringable
{
    public function __construct(private readonly string $key, private readonly string $value)
    {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
