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

final class CallArgument
{
    public function __construct(private readonly string $value, private readonly string|null $name)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string|null
    {
        return $this->name;
    }
}
