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

namespace phpDocumentor\MetadataExample\Reflection;

use phpDocumentor\Reflection\Metadata\Metadata;

final class ExampleMetadata implements Metadata
{
    public function __construct(private readonly string $value)
    {
    }

    public function key(): string
    {
        return 'metadata_example';
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
