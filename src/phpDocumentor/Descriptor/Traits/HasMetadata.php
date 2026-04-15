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

use phpDocumentor\Reflection\Metadata\Metadata;

trait HasMetadata
{
    /** @var Metadata[] */
    private array $metadata = [];

    /** @param Metadata[] $metadata */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /** @return Metadata[] */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
