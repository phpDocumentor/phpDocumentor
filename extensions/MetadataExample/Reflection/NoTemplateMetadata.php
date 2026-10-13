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

/** Metadata with a declared key for which the extension ships no template. */
final class NoTemplateMetadata implements Metadata
{
    public function __construct(private readonly string $value)
    {
    }

    public function key(): string
    {
        return 'metadata_notemplate';
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
