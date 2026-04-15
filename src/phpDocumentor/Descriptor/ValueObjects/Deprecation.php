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

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;

final class Deprecation
{
    public function __construct(private DescriptionDescriptor $description, private string|null $version = null)
    {
    }

    public function getDescription(): DescriptionDescriptor
    {
        return $this->description;
    }

    public function getVersion(): string|null
    {
        return $this->version;
    }
}
